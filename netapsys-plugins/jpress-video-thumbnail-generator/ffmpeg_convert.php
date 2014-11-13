<?php
//class utilitaire video / ffmpeg
require_once(ABSPATH . 'wp-admin/includes/image.php');
class FFMPEG_CONVERT{
    public $url;
    public $path;
    public $seconde;
    public $image_url;
    public $image_path;
    public $width;
    public $size;
    public $height;
    public $ffmpegpath;
    public $options;

    function __construct($videoURL){
        $this->options = jvtg_get_options();
        $this->url = $videoURL;
        $this->ffmpegpath = $this->options['root'] . 'ffmpeg';

        $uploadUrl = wp_upload_dir();
        $uppath = $uploadUrl['basedir'];
        $upurl= $uploadUrl['baseurl'];
        $path = str_replace($upurl,$uppath,$this->url);
        $this->path = $path;

        $this->width = $this->options['width'];
        $this->height = $this->options['height'];
        $this->size = $this->width . 'x'.$this->height;
        $this->seconde = $this->options['seconde'];

        $urlinfo = pathinfo($this->url);
        $this->image_url = $upurl . '/'. $urlinfo['filename'].'_vignette.jpg';
        $this->image_path = $uppath . '/'. $urlinfo['filename'] . "_vignette.jpg";
    }

    /**
     * convertit un video en image
     *
     */
    function convertVideo($sec = null){
        if(!is_file($this->image_path)){
        	if(!is_null($sec) && $sec>0){
        		$this->seconde = $sec;
        	}
            //$command = "$this->ffmpegpath  -itsoffset -$this->seconde  -i $this->path -vcodec mjpeg -vframes 1 -an -f rawvideo -s $this->size $this->image_path";
            $command = "$this->ffmpegpath  -itsoffset -$this->seconde  -i $this->path -vcodec mjpeg -vframes 1 -an -f rawvideo $this->image_path";
            exec($command);

            $video_id= $this->get_attachment($this->url);

            //insertion attachment
            $imginfo = pathinfo($this->image_path);
            $image_id = $this->insert_attachment($imginfo['basename'],NULL,$video_id);
            $fullsizepath = get_attached_file( $image_id );
            $metadata = wp_generate_attachment_metadata($image_id, $fullsizepath );
            wp_update_attachment_metadata( $image_id, $metadata );

            //attach
            set_post_thumbnail($video_id, $image_id);
        }
    }

    function removeVignette(){
        if(is_file($this->image_path)){
            exec("rm ". $this->image_path);
        }
        $id= $this->get_attachment($this->image_url);
        wp_delete_attachment($id);
    }

    /**
     * function qui insert un nouveau attachment dans la table posts.
     *
     * @param null $filename
     *
     * @return bool|int (ids attachment)
     *
     */
    function insert_attachment( $filename = null , $titre = NULL,$parent_id=NULL) {
        $wp_upload_dir = wp_upload_dir();
        $chemin_upload = $wp_upload_dir['baseurl'];
        // test si $filename n'est pas null
        if($filename == null){
            return false;
        }
        else{
            $guid = $chemin_upload . '/' . $filename;
            // Est-ce que l'attachment a enregistrer existe.
            if($attachment_id = veolia_get_attachement($guid)){
                return $attachment_id;
            }
            else{
                if(is_null($titre)){
                    $file = pathinfo($filename);
                    $titre = $file['filename'];
                }

                $attachment = array(
                    'guid' => $guid,
                    'post_mime_type' => get_mine_type($filename),
                    'post_title' => $titre,
                    'post_status' => 'inherit',
                    'post_type' => 'attachment'
                );

                remove_filter( 'pre_post_guid', 'wp_strip_all_tags' );
                remove_filter( 'pre_post_guid', 'esc_url_raw'       );
                remove_filter( 'pre_post_guid', 'wp_filter_kses'    );

                $id_attachment = wp_insert_attachment($attachment, $filename,$parent_id);
                return $id_attachment;
            }
        }
    }

    function get_attachment($url){
        global $wpdb;

        //rechercher par url
        $query = "SELECT ID FROM {$wpdb->prefix}posts WHERE " . $wpdb->prepare("guid='%s'", $url) ." AND post_type='attachment'";
        return $wpdb->get_var($query);
    }
}
?>