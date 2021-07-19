<?
class BdAdmin{

   static private $conexcion = NULL;
   static private $instancia = NULL;
   
   private function __construct() {
           self::$conexcion = mysql_connect('localhost', 'root', 'leo1979');
           mysql_select_db('trafico', self::$conexcion);
   }
   
   public function ejecutar($query){
          return mysql_query($query, self::$conexcion);
   }

   static public function getInstancia() {
       if (self::$instancia == NULL) {
          self::$instancia = new BdAdmin();
       }
       return self::$instancia;
   }
}
?>

