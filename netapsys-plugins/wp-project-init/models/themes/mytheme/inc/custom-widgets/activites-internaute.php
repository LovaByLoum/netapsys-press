<?php
class Activite_Internaute extends WP_Widget{
  function __construct() {
    parent::WP_Widget(FALSE,'Les Activités des internautes');
  }

  //fonction d'affichage dans le BO
  function form($instance){

  }

  //fonction de mis à jour
  function update($new_instance, $old_instance) {

  }

  //function de callbasck
  public function widget($args, $instance) {
    echo $before_widget;
    $doFB = false;
    if($doFB):?>
    	<div class="activites panel">
		      <div class="panel-header"></div>
		      <div class="panel-content">
		        <ul class="authored">
		          <li>
		            <span class="photo"> <img src="<?php echo get_template_directory_uri(); ?>/assets/images/profil.jpg" alt=""> </span>
		                <span class="commentaire">
		                    <div><strong>Elodie</strong> a posté(e) une astuce</div>
		                    <div class="light">Il y a 3 heures</div>
		                </span>
		          </li>
		          <li class="divider"></li>
		        </ul>
		      </div>
		      <div class="panel-footer"></div>
		    </div>
    <?php else:
    	$activities = user::get_activite();
      if($activities):
		    ?>
		    <div class="activites panel">
		      <div class="panel-header"></div>
		      <div class="panel-content">
		        <ul class="authored">
		        	<?php foreach ($activities as $key => $act):
		        		list($action, $user_id,$astuce_id,$date) = $act;
		        		$userdata= get_user_by('id',$user_id);
		        		$nom = $userdata->data->display_name;
                $pseudo = get_user_meta($user_id,'user_pseudo',true);
		        		$avatar = get_avatar($user_id,30);
		        		switch ($action){
		        			case 'favoris':
		        				$text = 'a mis en favoris';
		        				break;
		        			case 'vote':
		        				$text = 'a voté pour';
		        				break;
		        			case 'post':
		        				$text = 'a posté';
		        				break;
		        			case 'comment':
		        				$text = 'a commenté';
		        				break;
		        		}
		        		
		        		$astuce = '<a style="color:#333333;font-weight: bold;" href="' .get_permalink($astuce_id). '">une astuce</a>';
		        		
		        		$date = getRelativeTime($date);
		        	?>
			          <li>
			            <span class="photo"> <a href="<?php echo user::get_user_page_profil($user_id);?>"><?php echo $avatar;?> </a></span>
			                <span class="commentaire">
			                    <div><strong><a style="color:#333333;" href="<?php echo user::get_user_page_profil($user_id);?>"><?php echo (!empty($pseudo)?$pseudo:$nom);?></a></strong> <?php echo $text?> <?php echo $astuce;?></div>
			                    <div class="light"><?php echo $date;?></div>
			                </span>
			          </li>
			          <?php if($key!=sizeof($activities)-1):?>
			          	<li class="divider"></li>
			          <?php endif;?>
				    <?php endforeach;?>
		        </ul>
		      </div>
		      <div class="panel-footer"></div>
		    </div>
		    <?php
      endif;
	endif;
    echo $after_widget;
  }
}
?>
