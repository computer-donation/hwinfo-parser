<?php

namespace Tienvx\HwinfoParser;

class Parser
{
    protected array $priorKeys = [];
    protected array $device = [];
    protected int $indent = 0;
    protected ?string $lastKey = null;

    public function parse(string $path): array
    {
        $devices = [];
        $handle = fopen($path, 'r');

        while (! feof($handle)) {
            $line = fgets($handle);
            if (empty(trim($line))) {
                # End of device
                $devices[] = $this->device;
                $this->reset();
                continue;
            }
            switch (substr_count($line, ':')) {
                case 2:
                    # Begin of device
                    $this->reset();
                    break;
                case 1:
                    [$key, $value] = explode(':', trim($line));
                    $this->setValue($key, trim($value) ?: [], $this->getIndent($line));
                    break;
                case 0:
                    $this->addItem(trim($line), $this->getIndent($line));
                    break;
                default:
                    # Unknown
                    break;
            }
        }
        fclose($handle);

        return $devices;
    }

    protected function getIndent(string $text): int
    {
        return strspn($text, ' ') / 2;
    }

    protected function setValue(string $key, string|array $value, int $indent): void
    {
        if ($indent < $this->indent) {
            for ($i = 0; $i < $this->indent - $indent; $i++) {
                array_pop($this->priorKeys);
            }
        } elseif ($indent === $this->indent + 1) {
            if (!is_null($this->lastKey)) {
                array_push($this->priorKeys, $this->lastKey);
            }
        }
        $this->addValue($value, $key, $indent);
    }

    protected function addItem(string $item, int $indent): void
    {
        if (empty($this->device)) {
            # Skip this value
            return;
        }
        if ($this->lastKey !== null && $indent === $this->indent) {
            # Skip this value
            return;
        }

        if (!is_null($this->lastKey)) {
            array_push($this->priorKeys, $this->lastKey);
        }
        $this->addValue($item, null, $indent);
    }

    protected function addValue(string|array $value, ?string $key, int $indent): void
    {
        $array = &$this->getArray();
        if (!is_array($array)) {
            $array = [];
        }
        if ($key) {
            $array[$key] = $value;
        } else {
            $array[] = $value;
        }
        $this->lastKey = $key;
        $this->indent = $indent;
    }

    protected function &getArray(): array|string|null
    {
        $array = &$this->device;
        foreach ($this->priorKeys as $priorKey) {
            $array = &$array[$priorKey];
        }

        return $array;
    }

    protected function reset(): void
    {
        $this->priorKeys = [];
        $this->device = [];
        $this->indent = 0;
        $this->lastKey = null;
    }
}
