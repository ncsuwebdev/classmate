<?php
/**
 * This class uses the UNIX commandline tool "zip" to create a zip file
 * of the files specified by the addFiles() method.
 *
 */
class Zip
{
    /**
     * The path to the zip file to be created
     *
     * @var string
     */
    protected $_zipPath = "";
   
    /**
     * The files to add to the zip file
     *
     * @param array $files
     */
    protected $_files = array();
    
    
    /**
     * Initialize the class with the path to the zip file to be created.
     *
     */
    public function __construct($filepath)
    {      
        $this->_zipPath = $filepath;
    }
    
    
    /**
     * Takes either a string or an array of strings of files to add to
     * the zip file.
     *
     * @param array or string $data
     */
    public function addFiles($file)
    {
        if (is_array($file)) {
            
            foreach ($file as $f) {
                $this->_files[] = $f;        
            }
            
        } else if (is_string($file)) {
            
            $this->_files[] = $file;
            
        } else {
            
            throw new Exception("File type must be a string");
        }
    }
    
    
    
    /**
     * Creates the zip file once all the files are added.
     * 
     * zip [-options] [-b path] [-t mmddyyyy] [-n suffixes] [zipfile list] [-xi list]
     * The default action is to add or replace zipfile entries from list, which
     * can include the special name - to compress standard input.
     * If zipfile and list are omitted, zip compresses stdin to stdout.
     * -f   freshen: only changed files  -u   update: only changed or new files
     * -d   delete entries in zipfile    -m   move into zipfile (delete files)
     * -r   recurse into directories     -j   junk (don't record) directory names
     * -0   store only                   -l   convert LF to CR LF (-ll CR LF to LF)
     * -1   compress faster              -9   compress better
     * -q   quiet operation              -v   verbose operation/print version info
     * -c   add one-line comments        -z   add zipfile comment
     * -@   read names from stdin        -o   make zipfile as old as latest entry
     * -x   exclude the following names  -i   include only the following names
     * -F   fix zipfile (-FF try harder) -D   do not add directory entries
     * -A   adjust self-extracting exe   -J   junk zipfile prefix (unzipsfx)
     * -T   test zipfile integrity       -X   eXclude eXtra file attributes
     * -y   store symbolic links as the link instead of the referenced file
     * -R   PKZIP recursion (see manual)
     * -e   encrypt                      -n   don't compress these suffixes
     */
    public function createZipFile()
    {   
          
        $list = implode("%", $this->_files);
        $list = str_replace('%', '" "', $list);
        $list = '"' . $list . '"';
        
        if (is_file($this->_zipPath)) {
            unlink($this->_zipPath);
        }
        
        $date = strftime('%m%d%Y', time());
        
        $cmd = "zip -j -t $date $this->_zipPath $list";
        
        exec($cmd, $output, $rc);
    }
}