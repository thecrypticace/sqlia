<?php

namespace Sqlia\Disks;

class HfsPlus extends AbstractDisk
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

        $sectors = 2048 * (int) preg_replace("/(\d+)M/", "$1", $size);

        $diskID = $this->cli->run("hdiutil attach -nomount ram://$sectors");
        output($diskID);
        output($this->cli->run("newfs_hfs -v 'sqlia' {$diskID}"));
        output($this->cli->run("mkdir -p {$root}"));
        output($this->cli->asRoot()->run("mount -o noatime -t hfs {$diskID} {$root}"));

        return $this->mounted($options);
    }

    public function unmount($options = []) : bool
    {
        $root = $options["path"] ?? $this->root();

        $diskID = $this->cli->asRoot()->run("diskutil info {$root} | grep 'Device Node:' | awk '{print $3}'");
        output($diskID);
        output($this->cli->asRoot()->run("umount -f {$root}"));
        output($this->cli->asRoot()->run("hdiutil detach {$diskID}"));
        output($this->cli->asRoot()->run("rm -rf {$root}"));

        return ! file_exists($root);
    }
}
