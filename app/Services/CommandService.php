<?php

namespace App\Services;

use App\Enums\ParseType;
use App\Traits\LoggerTrait;

class CommandService
{
    use LoggerTrait;

    /** Checks if input string is command.
     *
     * @param string|null $command
     * @return bool
     */
    public function checkIfCommand(?string $command): bool
    {
        if (is_null($command)) {
            $this->writeErrorLog('Command is null');
            return false;
        } elseif (strpos($command, '/') === 0) {
            return true;
        } else {
            $this->writeErrorLog('/ symbol is not at 0 position');
            return false;
        }
    }

    /** Checks if input string is command.
     *
     * @param string|null $value
     * @return bool
     */
    public function checkIfPressedButtonValue(?string $value): bool
    {
        if (is_null($value)) {
            $this->writeErrorLog('Null inputted value');
            return false;
        } elseif (strpos($value, '#') === 0) {
            return true;
        } else {
            $this->writeErrorLog('# symbol is not at 0 position');
            return false;
        }
    }

    /** Parses the commandline.
     *
     * @param string|null $commandLine
     * @param array|null $arguments
     * @param ParseType|null $parseType
     * @return bool|string
     */
    public function parse(?string $commandLine, ?array &$arguments = null, ?ParseType &$parseType = null): bool|string
    {
        $codes = ['/', '#'];
        $this->writeInfoLog('Got command line', [
            'command line' => $commandLine,
            'available codes' => $codes
        ]);

        if (is_null($commandLine)) {
            $this->writeErrorLog('Null inputted value');
            return false;
        }

        foreach ($codes as $code) {
            if (str_starts_with($commandLine, $code)) {
                switch ($code) {
                    case '/':
                        {
                            $this->writeInfoLog('Command determined', [
                                'command line' => $commandLine,
                                'starts with symbol /' => str_starts_with($commandLine, $code),
                                'code' => $code
                            ]);
                            $parseType = ParseType::command;
                            return $this->commandLineParser($commandLine, $arguments);
                    }
                    case '#':
                        {
                            $this->writeInfoLog('Pressed button value determined', [
                                'command line' => $commandLine,
                                'starts with symbol #' => str_starts_with($commandLine, $code),
                                'code' => $code
                            ]);
                            $parseType = ParseType::pressed_button_value;
                            return $this->commandLineParser($commandLine, $arguments);
                    }
                    default:
                        $this->writeErrorLog('Not command nor pressed button value');
                }
            }
        }
        return false;
    }

    /** Helper for the command line parser method.
     *
     * @param string $commandLine
     * @param array|null $arguments
     * @return bool
     */
    private function commandLineParser(string $commandLine, ?array &$arguments = null): bool|string
    {
        $commandLine = substr($commandLine, 1, strlen($commandLine) - 1);

        $commandLineItems = explode(' ', $commandLine);
        if (count($commandLineItems) > 1) {
            $commandName = $commandLineItems[0];
            unset($commandLineItems[0]);
            $arguments = $commandLineItems;
            $this->writeInfoLog('Command line parser executed', [
                'command name' => $commandName,
                'arguments' => $arguments
            ]);
            return $commandName;
        } elseif (count($commandLineItems) == 1) {
            $this->writeInfoLog('Command line parser executed', [
                'command name' => $commandLineItems[0],
                'arguments' => $arguments
            ]);
            return $commandLineItems[0];
        } else {
            return false;
        }
    }

    /** Parses json from command line.
     *
     * @param string $commandLine
     * @param array|null $arguments
     * @return bool
     */
    private function commandLineJsonParser(string $commandLine, ?array &$arguments = null): bool|string
    {
        $commandLineArray = json_decode($commandLine, true);

        if (sizeof($commandLineArray) > 0) {
            if (isset($commandLineArray['command']) && isset($commandLineArray['arguments'])) {
                $commandName = $commandLineArray['command'];
                $arguments = $commandLineArray['arguments'];

                $this->writeInfoLog('Json command line has parsed', [
                    'command name' => $commandName,
                    'arguments' => $arguments
                ]);
                return $commandName;
            } else {
                $this->writeErrorLog('Command key or arguments key are absent');
                return false;
            }
        } else {
            $this->writeErrorLog('Empty command line');
            return false;
        }
    }
}
