<?php
namespace Swoole\Websocket;

/**
 * @since 4.2.8
 */
class Closeframe extends \swoole_websocket_frame
{


    /**
     * @return mixed
     */
    public function __toString(){}

    /**
     * @param $data[required]
     * @param $opcode[optional]
     * @param $finish[optional]
     * @param $mask[optional]
     * @return mixed
     */
    public static function pack($data, $opcode=null, $finish=null, $mask=null){}

    /**
     * @param $data[required]
     * @return mixed
     */
    public static function unpack($data){}


}
