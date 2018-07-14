<?php

class CachingController {

    protected $cache;
    public function __construct($cache) {
        $this->cache = $cache;
    }

    public function check($data) {
	    $key = md5($data['category'] . '-' . $data['country'] . '-' . $data['keyword'] . '-' . $data['page_size'] . '-' . $data['page']);
            $item = $this->cache->getItem($key);
            return ($item->isHit()) ? json_decode($item->get()) : false;
    }

    public function add($data, $output) {
	    $key = md5($data['category'] . '-' . $data['country'] . '-' . $data['keyword'] . '-' . $data['page_size'] . '-' . $data['page']);
            $item = $this->cache->getItem($key);
	    $item->set(json_encode($output));
            $item->expiresAfter(600);
            $this->cache->save($item);
	    return true;
    }
}
