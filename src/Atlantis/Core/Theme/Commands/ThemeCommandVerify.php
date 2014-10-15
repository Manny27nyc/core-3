<?php namespace Atlantis\Core\Theme\Commands;

use Atlantis\Core\Module\Environment as Module;
use Illuminate\Console\Command;
use Illuminate\Filesystem\Filesystem as File;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Input\InputArgument;


class ThemeCommandVerify extends Command {

    /**
     * The console command name.
     *
     * @var string
     */
    protected $name = 'atlantis:theme-verify';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Theme structure verification';

    /**
     * The folders will be created.
     *
     * @var array
     */
    protected $folders = [
        'public/components/' => 'diagnoseComponents',
        'public/themes/' => 'diagnoseThemes'
    ];


    /**
     * Create a new command instance.
     *
     * @return void
     */
    public function __construct(File $file)
    {
        $this->file = $file;
        parent::__construct();
    }

    /**
     * Execute the console command.
     *
     * @return mixed
     */
    public function fire()
    {
        foreach($this->folders as $folder => $foreman){
            $check_path = base_path() . '/' . $folder;

            //@info Force components diagnose
            if($this->option('force-components')){
                $this->diagnoseComponents();
                unset($this->folders['public/components/']);
                return;
            }

            if( !$this->file->exists($check_path) ){
                $this->error("PROBLEM! Folder $folder not exist, starting diagnostic tool..");
                if( method_exists($this, $foreman) ) $this->{$foreman}();

            }else{
                $this->info("GOOD! Folder $folder exist.");

                //@info Check for folder contents
                if( count($this->file->allFiles($check_path)) == 0 ){
                    if( $this->confirm("WARNING! Folder $folder is empty, force diagnose? : [yes|no]") ){
                        if( method_exists($this, $foreman) ) $this->{$foreman}();
                    };
                }
            };
        }
    }

    protected function diagnoseComponents(){
        $folders = ['components','vendor/atlantis/core/public/components','workbench/atlantis/core/public/components'];
        $this->info('Diagnosing components folder..');

        foreach($folders as $folder){
            $check_path = base_path()."/$folder";
            if( $this->isFolderWithStructure($check_path) ){
                $this->info('Components folder founded : '.$folder);
                return $this->copyFolderWithAsk($check_path,base_path()."/public/components");
            }
        }

        $this->info('FATAL! Source component folder not found!');
    }


    protected function isFolderWithStructure($path){
        if( count(glob("$path/*",GLOB_ONLYDIR)) > 0 ){
            return true;
        }

        return false;
    }


    protected function copyFolderWithAsk($source,$destination){
        if( !$this->option('auto') ){
            $title_source = str_replace(base_path(),'', $source);
            $title_destination = str_replace(base_path(),'', $destination);
            if( !$this->confirm("Copy contents from $title_source to $title_destination ?[yes|no]") ) return;;
        }

        $this->file->copyDirectory($source,$destination);
        $this->info('File content copy completed!');

        return true;
    }


    protected function getOptions(){
        return array(
            array('auto',null,InputOption::VALUE_OPTIONAL,'Do not prompt on action, system will decide. eg: "--auto=true"', null),
            array('force-components',null,InputOption::VALUE_OPTIONAL,'Force copy on components inspection. eg: "--force-components=true"', null)
        );
    }

}
