<?php

namespace App\Support;

class Option {
    private mixed $settingsFile;
    private $settings;
    private mixed $cacheEnabled;

    public function __construct($filePath = 'settings.json', $cacheEnabled = true) {
        $this->settingsFile = storage_path($filePath);
        $this->cacheEnabled = $cacheEnabled;
        $this->settings = null;

        $this->loadSettings();
    }

    private function loadSettings() {
        if ($this->settings === null || !$this->cacheEnabled) {
            if (!file_exists($this->settingsFile)) {
                file_put_contents($this->settingsFile, json_encode([]));
            }
            $json = file_get_contents($this->settingsFile);
            $this->settings = json_decode($json, true);
        }
    }

    private function saveSettings() {
        if ($this->settings !== null) {
            $json = json_encode($this->settings, JSON_PRETTY_PRINT);
            file_put_contents($this->settingsFile, $json);
        }
    }

    public function get($optionName, $defaultValue = null) {
        return isset($this->settings[$optionName]) ? $this->settings[$optionName] : $defaultValue;
    }

    public function set($optionName, $value) {
        $this->settings[$optionName] = $value;
        $this->saveSettings();
    }

    public function delete($optionName) {
        if (isset($this->settings[$optionName])) {
            unset($this->settings[$optionName]);
            $this->saveSettings();
        }
    }

    public function getAll() {
        return $this->settings;
    }

    public function clear() {
        $this->settings = [];
        $this->saveSettings();
    }

    public function exists($optionName) {
        return isset($this->settings[$optionName]);
    }

    public function setDefaults(array $defaults) {
        foreach ($defaults as $key => $value) {
            if (!isset($this->settings[$key])) {
                $this->settings[$key] = $value;
            }
        }
        $this->saveSettings();
    }

    public function setSettingsFile($filePath) {
        $this->settingsFile = $filePath;
        $this->settings = null; // Clear the current cache
        $this->loadSettings();
    }

    public function setCacheEnabled($enabled) {
        $this->cacheEnabled = $enabled;
        if (!$enabled) {
            $this->settings = null; // Clear the current cache
        }
    }
}

$option = new Option('settings.json', true);

