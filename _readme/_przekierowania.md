#Przekierowanie z powrotem
    return $this->redirect($request->server->get('HTTP_REFERER'));