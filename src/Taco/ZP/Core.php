<?php namespace Taco\ZP;

use muqsit\invmenu\InvMenuHandler;
use pocketmine\command\ConsoleCommandSender;
use pocketmine\level\Position;
use pocketmine\math\Vector3;
use pocketmine\plugin\Plugin;
use pocketmine\plugin\PluginBase;
use pocketmine\utils\Config;
use Taco\ZP\ce\CEManager;
use Taco\ZP\command\CommandManager;
use Taco\ZP\crates\CratesManager;
use Taco\ZP\gems\GemManager;
use Taco\ZP\kit\KitManager;
use Taco\ZP\random\EntityUtils;
use Taco\ZP\random\Forms;
use Taco\ZP\random\NumberUtils;
use Taco\ZP\random\PickaxeUtils;
use Taco\ZP\random\RankupUtils;
use Taco\ZP\session\SessionManager;
use Taco\ZP\task\AnnouncementsTask;
use Taco\ZP\task\ScoreboardTask;
use Taco\ZP\task\ScoreTagTask;
use Taco\ZP\task\UpdateMineText;
use Taco\ZP\text\CFTManager;
use Taco\ZP\vp\VotePartyManager;
use function array_diff;
use function array_rand;
use function scandir;

class Core extends PluginBase {

    private static self $instance;

    private static SessionManager $sessionManager;

    private static Forms $forms;

    private static RankupUtils $rankupUtils;

    private static EntityUtils $entityUtils;

    private static GemManager $gemManager;

    private static PickaxeUtils $pickaxeUtils;

    private static NumberUtils $numberUtils;

    private static CEManager $CEManager;

    private static VotePartyManager $votePartyManager;

    private static CratesManager $cratesManager;

    private static KitManager $kitManager;

    private static CFTManager $CFTManager;

    public Config $database;

    public array $config = [];

    public array $blocksInMine = [];

    public array $storedBlocksInMine = [];

    public Plugin $mineReset;

    public function onLoad() : void {
        self::$instance = $this;
    }

    public function onEnable() : void {
        foreach (array_diff(scandir($this->getServer()->getDataPath() . "worlds"), ["..", "."]) as $levelName) {
            $this->getServer()->loadLevel($levelName);
        }

        $this->config = $this->getConfig()->getAll();
        $this->database = new Config($this->getDataFolder()."data.yml", Config::YAML);
        self::$sessionManager = new SessionManager();
        self::$forms = new Forms();
        self::$rankupUtils = new RankupUtils();
        $entityUtils = new EntityUtils();
        self::$entityUtils = $entityUtils;
        $entityUtils->init();
        CommandManager::init();
        $gemMgr = new GemManager();
        self::$gemManager = $gemMgr;
        $gemMgr->init();
        if (!InvMenuHandler::isRegistered()) InvMenuHandler::register($this);
        self::$pickaxeUtils = new PickaxeUtils();
        self::$numberUtils = new NumberUtils();
        $ceMgr = new CEManager();
        self::$CEManager = $ceMgr;
        $ceMgr->init();
        $vp = new VotePartyManager();
        self::$votePartyManager = $vp;
        $vp->init();
        $kitMgr = new KitManager();
        self::$kitManager = $kitMgr;
        $kitMgr->init();
        $cft = new CFTManager();
        self::$CFTManager = $cft;

        $this->getServer()->dispatchCommand(new ConsoleCommandSender(), "mine reset-all");

        $mineReset = $this->getServer()->getPluginManager()->getPlugin("MineReset");
        $this->mineReset = $mineReset;
        foreach ($mineReset->getMineManager()->getMines() as $mine) {
            /*** @var $pointA Vector3 */
            $pointA = $mine->getPointA();
            /*** @var $pointB Vector3 */
            $pointB = $mine->getPointB();
            $blocks = ($pointB->getX() - $pointA->getX() + 1)*($pointB->getY() - $pointA->getY() + 1)*($pointB->getZ() - $pointA->getZ() + 1);
            $midX = ($pointA->getX() + $pointB->getX()) / 2;
            $midZ = ($pointA->getZ() + $pointB->getZ()) / 2;
            $y = $pointA->getY() > $pointB->getY() ? $pointA->getY() + 3 : $pointB->getY() + 3;

            $this->getLogger()->info("Mine Loaded: ".$mine->getName()." | ".$blocks." Blocks");
            $this->blocksInMine[$mine->getName()] = $blocks;
        }
        $this->storedBlocksInMine = $this->blocksInMine;

        $this->getServer()->getPluginManager()->registerEvents(new EventListener(), $this);

        $this->getScheduler()->scheduleRepeatingTask(new AnnouncementsTask(), 20 * 120);
        $this->getScheduler()->scheduleRepeatingTask(new ScoreboardTask(), 20 * 3);
        $this->getScheduler()->scheduleRepeatingTask(new ScoreTagTask(), 20 * 3);
        $this->getScheduler()->scheduleRepeatingTask(new UpdateMineText(), 20 * 10);

        $cratesMgr = new CratesManager();
        self::$cratesManager = $cratesMgr;
        $cratesMgr->init();

        if ($this->getServer()->isLevelGenerated("spawn")) $this->getServer()->setDefaultLevel($this->getServer()->getLevelByName("spawn"));

        $this->getServer()->getNetwork()->setName("§l§dZ§bPrisons§r§7");

        $this->getLogger()->notice("ZPrisons has loaded.");
    }

    public function onDisable() : void {
        foreach ($this->getServer()->getOnlinePlayers() as $player) {
            $player->kick("§l§dZ§bPrisons§r\n§eThe Server Has Restarted.\n§r§oplease join back after 30 seconds...", false);
        }
        foreach ($this->config as $name => $newValue) {
            $this->getConfig()->remove($name);
            $this->getConfig()->set($name, $newValue);
        }
        $this->getConfig()->save();
        sleep(5);
    }

    public static function getRandomPrefix() : string {
        $array = ["§l§d", "§l§b", "§l§e", "§l§c"];
        return $array[array_rand($array)]."§lZ§r§7 » §f";
    }

    public static function getInstance() : self {
        return self::$instance;
    }

    public static function getSessionManager() : SessionManager {
        return self::$sessionManager;
    }

    public static function getForms() : Forms {
        return self::$forms;
    }

    public static function getRankupUtils() : RankupUtils {
        return self::$rankupUtils;
    }

    public static function getEntityUtils() : EntityUtils {
        return self::$entityUtils;
    }

    public static function getGemManager() : GemManager {
        return self::$gemManager;
    }

    public static function getPickaxeUtils() : PickaxeUtils {
        return self::$pickaxeUtils;
    }

    public static function getNumberUtils() : NumberUtils {
        return self::$numberUtils;
    }

    public static function getCEManager() : CEManager {
        return self::$CEManager;
    }

    public static function getVotePartyManager() : VotePartyManager {
        return self::$votePartyManager;
    }

    public static function getCratesManager() : CratesManager {
        return self::$cratesManager;
    }

    public static function getKitManager() : KitManager {
        return self::$kitManager;
    }

    public static function getCFTManager() : CFTManager {
        return self::$CFTManager;
    }

}