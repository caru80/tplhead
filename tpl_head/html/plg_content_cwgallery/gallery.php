<?php
	/**
	 * CRu.: Müll-Code aus Template entfernt!
	 */

	$imagelist .= '<div class="cwgallery" id="cwgallery-'.$hash.'"> 
				<div class="calbum gallery lightgallery">
			';
					foreach($images as $key=>$photo) {
					if($show_desc_in_detail) {
						$title = $photo->caption;
						if($photo->description != '') {
						$title .= '<br/>'.str_replace('\n','<br/>',$photo->description);
						}
					} else {
						$title = $photo->caption;
					}
					
					if($lightbox == 'lightgallery'){
						$imagelist .= '<div data-sub-html="'.$title.'" data-src="'.JUri::root() . ltrim($photo->path,"/").'">';
					}
					$imagelist .= '<a class="cimage '.$caption_position.'" href="'.JUri::root() . ltrim($photo->path,"/").'" data-lightbox="photo-'.$article->id . '-'.$hash.'" data-title="'.$title.'">';
					$imagelist .= '   <span><img class="cphoto" src="'.JUri::root() . ltrim($photo->thumb,"/").'" alt="" title="'.$photo->caption.'"></span>';
					if($show_info_on_hover) {
						$imagelist .= '   <div class="cmask '.$caption_color.'"><span class="ccaption">';
						if($show_caption_on_hover) { $imagelist .= '     <span class="ctitle">'.$photo->caption.'</span>'; }
						if($show_desc_on_hover) { $imagelist .= '     <span class="cdesc">'.$photo->description.'</span>'; }
						$imagelist .= '   </span></div>';
					}                                                                                                                  
					$imagelist .= '</a>'; 
					if($lightbox == 'lightgallery') {
						$imagelist .= '</div>';
					}                 
					}
						
	$imagelist .= '
				</div> 
			</div>
			';
?>