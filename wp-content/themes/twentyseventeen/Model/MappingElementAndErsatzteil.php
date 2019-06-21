<?php
/**
 * Created by PhpStorm.
 * User: ywang
 * Date: 2019/4/29
 * Time: 8:46
 */

require_once (dirname(__FILE__) . '/../Model.php');

class MappingElementAndErsatzteil extends GoodOne_Model {

    /**
     * @var String EAN of Element
     */
    private $ean_elm;

    /**
     * @var String EAN of Ersatzteil
     */
    private $ean_est;

    /**
     * @var boolean
     */
    private $isExistInDB;

    /**
     * MappingElementAndErsatzteil constructor.
     */
    public function __construct(){
        parent::__construct();

        $args_num = func_num_args(); // Get the quantity of parameters

        // set the parameters to private variables
        if($args_num == 2){
            $this->setEanElm(func_get_arg(0));
            $this->setEanEst(func_get_arg(1));

            /*
            global $wpdb;
            $db_table_name = "ihmapping_elements_ersatzteile";
            $sql = "SELECT COUNT(*) FROM `".$db_table_name."` WHERE %s = %s";
            $resultsInDB = $wpdb->query($wpdb->prepare($sql, $this->getEanElm(), $this->getEanEst()));

            print_r($resultsInDB);
            */

        }else{
            exit('Func param not match.');
        }

    }

    public function my_func(){
        //
    }







    /**
     * @return String
     */
    public function getEanElm()
    {
        return $this->ean_elm;
    }

    /**
     * @param String $ean_elm
     */
    public function setEanElm($ean_elm)
    {
        $this->ean_elm = trim($ean_elm);
    }

    /**
     * @return String
     */
    public function getEanEst()
    {
        return $this->ean_est;
    }

    /**
     * @param String $ean_est
     */
    public function setEanEst($ean_est)
    {
        $this->ean_est = trim($ean_est);
    }

}