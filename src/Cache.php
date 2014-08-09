<?php
/**
 * store and retrieve data to be cached in json format
 * the writing time is stored and compared to the validity when fetching the cache.
 * if no current data is found, get() returns null
 */
use function Aoloe\debug as debug;
class Cache {
    private $path = 'cache/';
    private $file = null;
    private $data = null;
    private $timeout = 86400; // 60*60*24 = 1 day
    public function set_file($file) {$this->file = $this->path.$file;}
    public function set_timeout($time) {$this->timeout = $time;}

    public function is_current() {
        if (is_null($this->data)) {
            $cache = array (
                'time' => 0,
                'data' => null,
            );
            if (file_exists($this->file)) {
                $cache = file_get_contents($this->file);
                $cache = json_decode($cache, true);
            }
            if (isset($this->timeout) && (time() - $cache['time'] < $this->timeout)) {
                $this->data = $cache['data'];
            }
        }
        return isset($this->data);
    }

    public function get() {
        if (isset($this->data)) {
            return $this->data;
        } elseif ($this->is_current()) {
            return $this->data;
        } else {
            return null;
        }
    }

    public function put($data) {
        if (isset($this->file)) {
            $this->data = $data;
            $cache = array (
                'time' => time(),
                'data' => $data,
            );
            // debug('cache', $cache);
            file_put_contents($this->file, json_encode($cache));
        }
    }
}
