<?php
namespace AliOpenCore;

interface IAcsClient {
    /**
     * @param \AliOpenCore\AcsRequest $request
     * @return mixed
     */
    public function doAction($request);
}
