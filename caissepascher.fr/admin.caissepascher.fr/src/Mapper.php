<?php

    class Mapper extends SimplifiedDB{

        private $delta = 67392;

        function __construct() {

            /**** connect to database ****/               
            $this->dbConnect(DATABASE_HOST, DATABASE_USER, DATABASE_PASSWORD, DATABASE_BDD);

            /**** PDO require one extra step, parameter binding ****/
            $this->output_array = true;
        }
        
        /**
        * Génération aléatoire unique MD5 String pour utilisateur clé Api
        * @param String $key
        * @return String
        */
        protected function generateApiKey($key) {
            return sha1(uniqid($key, true));
        }

        
        /**
        * Retour la date actuel dans le format sql classic
        * @return String
        */
        public function DateTime($option = true) {
            $today = getdate();
            $today['wday'] = (strlen($today['wday']) == 1)?'0'.$today['wday']:$today['wday'];
            switch ($option) {
                case true:
                    return $today['year'].'-'.$today['mon'].'-'.$today['wday'].' '.$today['hours'].':'.$today['minutes'].':'.$today['seconds'];
                    break;
                case false:
                    return $today['year'].'-'.$today['mon'].'-'.$today['wday'];
                    break;
                default:
                    return $today;
            }
        }
        
        
        public function DateStrtotimeTime() {
            $today = strtotime(date("Y-m-d H:i:s"));
            return date("Y-m-d H:i:s", $today + $this->delta);
        }
        
        /**
        * Retour l'adresse ip de l'utilisateur
        * @return String
        */
        protected function get_ip() {
            // IP si internet partagé
            if (isset($_SERVER['HTTP_CLIENT_IP'])) {
                return $_SERVER['HTTP_CLIENT_IP'];
            }
            // IP derrière un proxy
            elseif (isset($_SERVER['HTTP_X_FORWARDED_FOR'])) {
                return $_SERVER['HTTP_X_FORWARDED_FOR'];
            }
            // Sinon : IP normale
            else {
                return (isset($_SERVER['REMOTE_ADDR']) ? $_SERVER['REMOTE_ADDR'] : '');
            }
        }

    }