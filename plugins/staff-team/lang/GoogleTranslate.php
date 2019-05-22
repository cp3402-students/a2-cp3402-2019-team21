<?php
/**
 * Description of GoogleTranslate
 *
 * @author lusinda
 */
class GoogleTranslate {
    /**
     * @var string Some errors
     */
    private $errors = "";

    /**
     * Get string translation from Google or cached file if exists
     * @param  string $text          Source text to translate
     * @param  string $fromLanguage  Source language
     * @param  string $toLanguage    Destenation language
     * @param  bool   $translit      If true function return transliteration of source text 
     * @return string|bool           Translated text or false if exists errors
     */
    public function translateText($text, $fromLanguage = "en", $toLanguage = "ru", $translit = false) {
        if (empty($this->errors)) {
            $translation = "";
            $textLength = strlen(trim($text));
            for ($i = 0; $i < strlen($textLength); $i += 1000) {
                $subText = substr($text, $i, 1000);

                $url = "http://translate.google.com/translate_a/t?client=" . time() . "&text=" . urlencode($subText) . "&tl=" . $toLanguage . "&sl=" . $fromLanguage;
                $response = $this->fileGetContentsUtf8($url);
                $translation .= $this->parseGoogleResponse($response, $toLanguage, $translit);
            }

            $result = new stdClass;
            $result->text = $translation;
            return $result;
        } else {
            return false;
        }
    }

    /**
     * Parse Google response.
     * @param  string $response      Response
     * @param  string $fromLanguage  Source language
     * @param  string $toLanguage    Destenation language
     * @param  bool   $translit      If true function return transliteration of source text 
     * @return string|bool           Translated text or false if exists errors
     */
    private function parseGoogleResponse($response, $toLanguage, $translit = false) {
        $response = str_replace(',,', ',"",', $response);
        $response = str_replace(',,', ',"",', $response);
        if (empty($this->errors)) {
            $result = "";
            if ($response == '') {
                return "";
            }
            $json = json_decode($response);
            if(is_array($json && count($json)>0)){
                foreach ($json->sentences as $sentence) {
                    $result .= $translit ? $sentence->translit : $sentence->trans;
                }
                return $result;
            }else{
                return "";
            }

        } else {
            return "";
        }
    }

    /**
     * Do request
     * @param  string $url  Url
     * @param  $set_utf8    Encode the result to utf8
     * @return string       Content of response
     */
    public function fileGetContentsUtf8($url, $set_utf8 = true) {
        $content = '';
        $charset = '';
        $utf8 = false;
        $user_agent = 'Mozilla/5.0 (Windows NT 6.1; WOW64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/33.0.1750.154 Safari/537.36';
        $file_get_enabled = preg_match('/1|yes|on|true/i', ini_get('allow_url_fopen'));
        if ($file_get_enabled) {
            $options = array('http' => array('user_agent' => $user_agent));
            $context = stream_context_create($options);
            $content = @file_get_contents($url, false, $context);
            $http_status = $http_response_header;
            if (!empty($http_response_header)) {
                foreach ($http_response_header as $header) {
                    if (substr(strtolower($header), 0, 13) == "content-type:") {
                        if (count(explode(";", $header)) > 1) {
                            list($contentType, $charset) = explode(";", $header);
                        }
                    }
                }
            }
            $headers = array();
            if(is_array(headers_list()) && count(headers_list())>0){
                $headers = headers_list();
            }
            // get the content type header
            foreach ($headers as $header) {
                if (substr(strtolower($header), 0, 13) == "content-type:") {
                    list($contentType, $charset) = explode(";", trim(substr($header, 14), 2));
                    if (strtolower(trim($charset)) == "charset=utf-8") {
                        $utf8 = true;
                    }
                }
            }
            if ($charset && strpos(strtolower($charset), 'utf-8')) {
                $utf8 = true;
            } else {
                $charset = mb_detect_encoding($content);
                if (strpos(strtolower($charset), 'utf-8')) {
                    $utf8 = true;
                }
            }

            if (!$utf8 && $set_utf8) {
                $content = utf8_encode($content);
            }
        } else {
            $this->errors = array('status' => 'warning', 'msg' => 'Could not make HTTP request: Please set \'allow_url_open\' in php.ini');
        }
        return $content;
    }
}
