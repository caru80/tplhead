        <?php
        if($item->kachel_headingonly):
        ?>
                    <div class="row-header">
                        <?php
                            if(!empty($item->kachel_title)):
                        ?>    
                                <h3><?php echo $item->kachel_title;?></h3>
                        <?php
                            endif;
                        ?>
                        <?php
                            if(!empty($item->kachel_description)):
                        ?>
                                <div class="intro">
                                    <?php echo $item->kachel_description;?>                                
                                </div>
                        <?php
                            endif;
                        ?>
                    </div>
        <?php
                else:

                    $classList = array();
                    $classList[] = !empty($item->tile_class) ? $item->tile_class : '';
        ?>  
                    <div class="column <?php echo !empty($item->grid_column_size) ? ' ' . $item->grid_column_size : ' item-column';?>">
                        <div class="item<?php echo count($classList) > 0 ? ' ' . implode(' ', $classList) : '';?>">
							<?php
								// -- Titel
								if(!empty($item->kachel_title)):
							?>
									<header class="item-header">
										<h4>
											<?php echo $item->kachel_title;?>
										</h4>
									</header>
							<?php
								endif;
							?>
							
							<?php
								// -- Einleitungsbild
								if($item->kachel_image):
							?>
									<div class="item-image-intro">
										<img src="<?php echo $item->kachel_image;?>" alt="<?php echo $item->kachel_title;?>" />
									</div>
							<?php
								endif;
							?>
                            <div class="item-introtext" itemprop="articleBody">
                                <?php echo $item->kachel_description; ?>
                            </div>
                        </div>
                    </div>
        <?php
                endif;
        ?>