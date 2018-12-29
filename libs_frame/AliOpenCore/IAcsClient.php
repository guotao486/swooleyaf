<?php
namespace AliOpenCore;

interface IAcsClient {
    /**
     * @param AcsRequest $request
     * @return mixed
     */
    public function doAction($request);
}
