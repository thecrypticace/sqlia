<?php

namespace Sqlia\Disks;

class TmpFs extends AbstractDisk
{
    public function mounted($options = []) : bool
    {
        $root = $options["root"] ?? $this->root();

        return file_exists($root) && is_dir($root);
    }

    public function mount($options = []) : bool
    {
        $size = $options["size"] ?? "512M";
        $root = $options["root"] ?? $this->root();

        $this->cli->asRoot()->run("mkdir -p {$root}");
        $this->cli->asRoot()->run("mount -t tmpfs -o size={$size} tmpfs {$root}");

        return $this->mounted($options);
    }

    public function unmount($options = []) : bool
    {
        $root = $options["path"] ?? $this->root();

        sleep(2);
        $this->cli->asRoot()->run("umount -l {$root}");
        $this->cli->asRoot()->run("rm -rf {$root}");

        return ! file_exists($root);
    }
}
