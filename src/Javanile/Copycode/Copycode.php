<?php


//

namespace Javanile\Copycode;

//
class Copycode
{
    /**
     * @var type
     */
    private $file = './copycode.json';


    private $json = [];


    public function cli($argv)
    {
        //
        if (!isset($argv[1])) {
            $this->syntaxError();
            exit();
        }

        //
        switch ($argv[1]) {
            //
            case '--version':
                echo
                "\n".
                "  Copycoder v0.0.7\n".
                "  by Francesco Bianco <bianco@javanile.org>\n".
                "\n";

                return;

            //
            case '--help':
                echo
                "\n".
                "  COPYCODE USAGE\n".
                "  --------------\n\n".
                "  copycode <task-name>       run copy task by <task-name>\n".
                "                             defined into copycode.json file\n".
                "                             placed in current path.\n".
                "\n".
                "  copycode --touch           create copycode.json file if not exists.\n".
                "\n".
                "  copycode --list            show the list of tasks.\n".
                "\n".
                "  copycode --help            show this shortest guide.\n".
                "\n".
                "  copycode --version         show credits and version.\n";

                return;

            //
            case '--list': $this->listTasks();

return;

            //
            case '--touch': $this->touch();

return;
        }

        //
        $taskname = implode(' ', array_splice($argv, 1));

        //
        if (!$this->file_exists()) {
            $this->error('copycode.json file not found.');
            exit();
        }

        //
        $this->file_decode();

        //
        if (!isset($this->json[$taskname])) {
            $this->error("task '".$taskname."' not found in copycode.json.");
            exit();
        }

        // single task
        if (isset($this->json[$taskname]['from'])
        && isset($this->json[$taskname]['to'])) {
            $this->runTask($this->json[$taskname]);
        }

        // grouped task
        elseif (is_array($this->json[$taskname])
        && count($this->json[$taskname]) > 0) {
            foreach ($this->json[$taskname] as $task) {
                $this->runTask($task);
            }
        }

        //
        echo "  (!) Task complete.\n";
    }


    private function file_exists()
    {
        //
        return file_exists($this->file);
    }


    private function file_decode()
    {
        //
        $this->json = json_decode(file_get_contents($this->file), true);
    }


    private function runTask($task)
    {
        //
        if (isset($task['name']) && $task['name']) {
            echo
            "\n".
            '  '.$task['name']."\n".
            '  '.str_repeat('-', strlen($task['name']))."\n";
        }

        //
        if (isset($task['description']) && $task['description']) {
            echo '  '.$task['description']."\n";
        }

        //
        $from = realpath($task['from']);

        //
        if (!$from) {
            $this->error('Error: no real path: '.$task['from']);

            return;
        }

        //
        if (!is_dir($from)) {
            $this->error('Error: not is a directory: '.$task['from']);

            return;
        }

        //
        $to = realpath($task['to']);

        //
        if (!$to) {
            $this->error('Error: no real path: '.$task['to']);

            return;
        }

        //
        if (!is_dir($to)) {
            $this->error('Error: not is a directory: '.$task['to']);

            return;
        }

        //
        echo
        "\n".
        '  - from: '.$this->fixstr($from)."\n".
        '  -   to: '.$this->fixstr($to)."\n\n";

        //
        $exclude = [$to];

        //
        if (isset($task['exclude']) && is_array($task['exclude'])) {
            foreach ($task['exclude'] as $path) {
                $exclude[] = realpath($from.'/'.$path);
            }
        }

        //
        $this->copyDir($from, $to, '', $exclude);

        //
        echo "\n";
    }


    private function copyDir($from, $to, $rel, $exlcude)
    {
        // destionation folder to copy file
        $to_rel = $to.'/'.$rel;

        // create current destination folder to copy file
        if (!is_dir($to_rel)) {
            mkdir($to_rel);
        }

        //
        $from_rel = $from.'/'.$rel;

        //
        $files = scandir($from_rel);

        //
        foreach ($files as $file) {
            //
            if (in_array($file, ['.', '..'])) {
                continue;
            }

            //
            $file_rel = $rel ? $rel.'/'.$file : $file;

            //
            $from_file_rel = realpath($from.'/'.$file_rel);

            //
            if (is_array($exlcude) && in_array($from_file_rel, $exlcude)) {
                //
                continue;
            }

            //
            if (is_dir($from_file_rel)) {
                //
                $this->copyDir($from, $to, $file_rel, $exlcude);
            }

            //
            else {
                //
                $to_file_rel = $to.'/'.$file_rel;

                //
                $this->copyFile($file_rel, $from_file_rel, $to_file_rel);
            }
        }
    }


    private function copyFile($file_rel, $from_file_rel, $to_file_rel)
    {
        //
        if (!file_exists($to_file_rel)) {
            //
            echo '    copy: '.$this->fixstr($file_rel)."\n";

            //
            copy($from_file_rel, $to_file_rel);
        }

        //
        else {
            //
            $srcTime = filemtime($from_file_rel);

            //
            $dirTime = filemtime($to_file_rel);

            //
            if ($srcTime > $dirTime) {
                //
                echo '    copy: '.$this->fixstr($file_rel)."\n";

                //
                copy($from_file_rel, $to_file_rel);
            }
        }
    }


    private function listTasks()
    {
        //
        if (!$this->file_exists()) {
            $this->error('copycode.json file not found.');
            exit();
        }

        //
        $this->file_decode();

        //
        if (!$this->json || !is_array($this->json)) {
            $this->error('file copycode.json corrupted.');
            exit();
        }

        //
        foreach ($this->json as $taskname => $task) {
            //
            echo
            "\n".
            "  task: {$taskname}\n".
            '  '.str_repeat('-', strlen($taskname) + 6)."\n";

            //
            if (isset($task['name'])) {
                echo '  '.$task['name']."\n";
            }
            if (isset($task['description'])) {
                echo '  '.$task['description']."\n";
            }

            //
            //echo "\n";
        }
    }


    private function touch()
    {
        //
        if ($this->file_exists()) {
            $this->error('No touch, copycode.json file already exists.');
            exit();
        }

        //
        $json = '{
    "task name": 
    {           
        "name"        : "",
        "description" : "",
        "from"        : "",
        "to"          : "",
        "exclude"     : []
    }
}';

        //
        if (!file_put_contents($this->file, $json)) {
        }

        //
        echo '  copycode.json file ready!';
    }

    /**
     * @param type $str
     *
     * @return type
     */
    private function fixstr($str)
    {
        //
        return strlen($str) > 61 ? '...'.substr($str, -61) : $str;
    }


    private function syntaxError()
    {
        //
        echo
        "\n".
        "  Syntax error.\n".
        "  Type: copycode --help\n";
    }


    private function error($msg)
    {
        //
        echo
        "\n".
        "  (?) {$msg}\n";
    }
}
