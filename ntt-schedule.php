<?php
/**
 * Shortcode Noo Schedule
 *
 * This template can be overridden by copying it to yourtheme/noo-timetable/shortcodes/ntt-schedule.php.
 *
 * @author      NooTheme
 * @package     NooTimetable/Templates/Shortcodes
 * @version     1.0.0
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly
}

if ( ! function_exists( 'shortcode_ntt_schedule' ) ) :

	function shortcode_ntt_schedule( $atts ) {

		if ( defined( 'DOING_AJAX' ) && $_REQUEST['action'] == 'wpseo_filter_shortcodes' ) {
			return;
		}
		$new_atts = shortcode_atts( array(
			'title'                        => '',
			'sub_title'                    => '',
			'schedule_layout'              => 'grid',
			'min_time'                     => '01:00:00',
			'max_time'                     => '24:00:00',
			'content_height'               => '',
			'source'                       => 'class',
			'default_view'                 => 'agendaWeek',
			'event_cat'                    => 'all',
			'teacher_of_class'             => 'no',
			'address_of_class'			   => 'no',
			//Add new at version 2.0.4.7
			'item_limit'                   => 2,
			//end
			'hide_time_range'              => '',
			'class'                        => '',
			'show_time_column'             => NOO_Settings()->get_option('noo_schedule_general_header_time_column', 'yes'),
			'show_weekends'                => NOO_Settings()->get_option('noo_schedule_general_header_weekends', 'yes'),
			'show_cate_filter'             => NOO_Settings()->get_option('noo_schedule_general_header_filter', 'yes'),
			'filter_layout'                => 'list',
			//Add new at version 2.0.4.7
			'filter_type'                  => 'category',
			'class_cat'                    => 'all',
			'class_level'                  => 'all',
			'class_trainer'                => 'all',
			'show_filter'                  => 'yes',
			'class_filter_layout'          => 'list',
			'class_show_all_tab'                 => 'yes',
			'show_all_tab'                 => 'yes',
			//end
			'general_header_toolbar'       => NOO_Settings()->get_option('noo_schedule_general_header_toolbar', 'yes'),
			'general_header_day'           => NOO_Settings()->get_option('noo_schedule_general_header_day', 'yes'),
			'custom_general_default_date'  => 'false',
			'general_default_date'         => NOO_Settings()->get_option('noo_schedule_general_default_date', ''),
			'general_navigate_link'        => NOO_Settings()->get_option( 'noo_schedule_general_navigate_link', 'internal' ),
			'general_popup'                => NOO_Settings()->get_option( 'noo_schedule_general_popup', 'yes' ),

			'general_popup_time'           => NOO_Settings()->get_option( 'noo_schedule_general_popup_time', 'yes' ),
			'general_popup_title'          => NOO_Settings()->get_option( 'noo_schedule_general_popup_title', 'yes' ),
			'general_popup_level'          => NOO_Settings()->get_option( 'noo_schedule_general_popup_level', 'yes' ),
			'general_popup_thumb'          => NOO_Settings()->get_option( 'noo_schedule_general_popup_thumb', 'yes' ),
			'general_popup_adress_trainer' => NOO_Settings()->get_option( 'noo_schedule_general_popup_adress_trainer', 'yes' ),
			'general_popup_excerpt'        => NOO_Settings()->get_option( 'noo_schedule_general_popup_excerpt', 'yes' ),
			'general_popup_style'          => NOO_Settings()->get_option( 'noo_schedule_general_popup_style', '1' ),

			'show_export'                  => NOO_Settings()->get_option( 'noo_schedule_general_show_export', 'no' ),
			'show_category'                => NOO_Settings()->get_option( 'noo_schedule_general_show_category', 'no' ),

			'class_show_category'      	   => NOO_Settings()->get_option( 'noo_schedule_class_show_category', 'yes' ),
			'class_item_style'             => NOO_Settings()->get_option( 'noo_schedule_class_item_style', '' ),
			'class_show_icon'              => NOO_Settings()->get_option( 'noo_schedule_class_show_icon', 'no' ),

			'event_split'                  => NOO_Settings()->get_option( 'noo_schedule_event_split', 'yes' ),
			'event_item_style'             => NOO_Settings()->get_option( 'noo_schedule_event_item_style', 'background_color' ),
			'event_show_icon'              => NOO_Settings()->get_option( 'noo_schedule_event_show_icon', 'yes' ),

			'general_header_background'    => NOO_Settings()->get_option( 'noo_schedule_general_header_background', '#cf3d6f' ),
			'general_header_color'         => NOO_Settings()->get_option( 'noo_schedule_general_header_color', '#fff' ),
			'general_today_column'         => NOO_Settings()->get_option( 'noo_schedule_general_today_column', '#fcf8e3' ),
			'general_holiday_background'   => NOO_Settings()->get_option( 'noo_schedule_general_holiday_background', '#fcf8e3' ),

		), $atts );
		extract( $new_atts );
		
		$id        = uniqid('noo_class_schedule_');
		$filter_id = uniqid('schedule_filter_');
		$js_data_from = uniqid('js_data_from_');
		$js_data_to = uniqid('js_data_to_');
		$export_id = uniqid('export_timetable_');
		$popup_id  = uniqid('popup_timetable_');

		if($source == 'class') {
			$filter_layout = $class_filter_layout;
		}
		$view = '';
		if( $schedule_layout == 'grid' ) {
			if( $default_view == 'agendaDay' )
			{
				$view = 'agendaDay';
				/*if($source == 'both') {
					$view = 'agendaWeek';
				}*/
			} else if( $default_view == 'agendaWeek' ) {
				$view = 'agendaWeek';
			} else {
				$view = 'month';
			}
		} else {
			if( $default_view == 'agendaDay' )
			{
				$view = 'listDay';
				if($source == 'both') {
					$view = 'listWeek';
				}
			} else if( $default_view == 'agendaWeek' ) {
				$view = 'listWeek';
			} else {
				$view = 'listMonth';
			}
		}

		if ($min_time === $max_time) {
			$min_time = '01:00:00';
			$max_time = '24:00:00';
		}

		/*Style Control Class*/
		$class_shortcode = ($class != '') ? $class . ' ' : '';
		if ( $show_time_column == 'yes' )
			$class_shortcode .= 'noo-class-schedule-shortcode';
		else
			$class_shortcode .= 'noo-class-schedule-shortcode hide-time-column';

		if ( $class_item_style == 'cat_bg_color' )
			$class_shortcode .= ' background-event';

		if ( $source == 'event' ) {
			$class_shortcode .= ' event-style';
		}

		$class_style_id = $id;

		wp_enqueue_style('calendar');
		wp_enqueue_script('calendar');
		wp_enqueue_script('calendar-lang');
		wp_enqueue_script('scheduler');

		wp_enqueue_style('nifty-modal');
		wp_enqueue_script('nifty-modal');

		// wp_enqueue_script('ics');
		wp_enqueue_script('ics-deps');

		ob_start();
		?>
        <div class=" <?php echo esc_attr( $class_style_id ); ?>">
            <div class=" <?php echo esc_attr( $class_shortcode ); ?>">
				<?php
				global $title_var;
				$title_var = compact('title', 'sub_title');
				noo_timetable_get_template( 'shortcodes/ntt-title.php' );
				?>

                <!-- Section content -->
				<?php
				// Get Default Date
				$time_default_date = (  $general_default_date != '' && ! is_numeric($general_default_date ) ) ? strtotime($general_default_date) : $general_default_date;

				// if ( $custom_general_default_date == 'true' ) {
				// 	if ( $general_default_date != '' && is_numeric($general_default_date ) ) {
				// 		$time_default_date = '';
				// 	}
				// }

				// if ( $custom_general_default_date == 'false' ) {
				// 	$time_default_date = '';
				// }
				$default_date = $time_default_date != '' ? $time_default_date : noo_timetable_time_now();
				$time_format = Noo__Timetable__Class::convertPHPToMomentFormat( get_option('time_format') );
				$date_format = Noo__Timetable__Class::convertPHPToMomentFormat( get_option('date_format') );

				if($default_view == 'month') {
					$first_month_day = date('Y-m-01', $default_date);
					$last_month_day = date('Y-m-t', $default_date);
					$from_date = $first_month_day;
					$to_date = $last_month_day;

					// Create nav
					$prev_from = date('Y-m-d',( strtotime ( '-1 month' , strtotime ( $from_date ) ) ) );
					$prev_to = date('Y-m-t', strtotime($prev_from));

					$next_from = date('Y-m-d',( strtotime ( '+1 month' , strtotime ( $from_date ) ) ) );
					$next_to = date('Y-m-t', strtotime($next_from));
				} else if($default_view == 'agendaWeek') {
					if ( get_option('start_of_week') == date( "w", $default_date ) ) {
						$first_week_day = date('Y-m-d', $default_date);
					} else {
						$start_of_week = Noo__Timetable__Class::_get_week_day( get_option('start_of_week') );
						$first_week_day = date( 'Y-m-d', strtotime('last ' . $start_of_week, $default_date) );
					}
					$end_week_day = date( 'Y-m-d', strtotime($first_week_day . ' +6 days') );
					$from_date = $first_week_day;
					$to_date = $end_week_day;

					// Create nav
					$prev_from = date('Y-m-d',( strtotime ( '-1 week' , strtotime ( $from_date ) ) ) );
					$prev_to = date('Y-m-d',( strtotime ( '-1 week' , strtotime ( $to_date ) ) ) );

					$next_from = date('Y-m-d',( strtotime ( '+1 week' , strtotime ( $from_date ) ) ) );
					$next_to = date('Y-m-d',( strtotime ( '+1 week' , strtotime ( $to_date ) ) ) );
				} else {
					$from_date = date('Y-m-d', $default_date);
					$to_date = date('Y-m-d', $default_date);

					$prev_from = date('Y-m-d',( strtotime ( '-1 days' , strtotime ( $from_date ) ) ) );
					$prev_to = date('Y-m-d',( strtotime ( '-1 days' , strtotime ( $to_date ) ) ) );

					$next_from = date('Y-m-d',( strtotime ( '+1 days' , strtotime ( $from_date ) ) ) );
					$next_to = date('Y-m-d',( strtotime ( '+1 days' , strtotime ( $to_date ) ) ) );
				}
				$holidays = array();
				if ( $source == 'event' ) {
					$data_filter_ids = $event_cat;
					$classes_arr = Noo__Timetable__Event::show_list_event($from_date, $to_date, $data_filter_ids, $new_atts);
					$classes_arr = $classes_arr['events_data'];

					$filter_list = Noo__Timetable__Event::show_category_event( $data_filter_ids );
					$action_filter = 'noo_event_filter';
					$action_mobile_filter = 'calendar_mobile';
					$show_all_tab = $show_all_tab;

				} elseif ( $source == 'class' ) {
					$doituong = new Noo__Timetable__Class();

					if($filter_type == 'category') {
						$data_filter_ids = $class_cat;
					} else if($filter_type == 'level') {
						$data_filter_ids = $class_level;
					} else {
						$data_filter_ids = $class_trainer;
					}
					$classes_arr = $doituong->show_schedule_class_list($from_date, $to_date, $data_filter_ids, $filter_type, $new_atts);
					$holidays = $classes_arr['holidays_data'];
					$classes_arr = $classes_arr['events_data'];

					$filter_list = $doituong->get_class_filter_list( $data_filter_ids, $filter_type );

					$action_filter = 'noo_class_filter';
					$action_mobile_filter = 'noo_class_responsive_navigation';

					//$holidays = $doituong->get_format_date_holidays();
					$show_all_tab = $class_show_all_tab;
				} else {
					$action_filter = 'noo_class_event_filter';
					$action_mobile_filter = 'noo_class_and_event';

					// Both Class and Event

					$doituong = new Noo__Timetable__Class();
					$classes_list = $doituong->show_schedule_class_list($from_date, $to_date, 'all', 'category', $new_atts);
					$holidays = $classes_list['holidays_data'];
					$classes_list = $classes_list['events_data'];
					$events_list = Noo__Timetable__Event::show_list_event( $from_date, $to_date, 'all', $new_atts);
					$events_list = $events_list['events_data'];
					$classes_arr = array_merge($classes_list, $events_list);

					$data_filter_ids = 'all';
					$filter_list = [];
					if($default_view == 'agendaDay') {
						$filter = new stdClass();
						$filter->id = 'all';
						$filter->title = 'Today';
						$filter_list[] = $filter;
					}
				}

				$content_height = is_numeric( $content_height ) ? $content_height : "'auto'";

				$header['next'] = 'next';
				$header['prev'] = 'prev';
				// RTL Options
				if ( is_rtl() ){
					$header['next'] = 'prev';
					$header['prev'] = 'next';
				}

				// Weekend option
				if( isset($show_weekends) && !is_array($show_weekends) ){
					$show_weekends = explode(',', $show_weekends);
				}
				if((is_array($show_weekends) && '' == $show_weekends['0']) || '' == $show_weekends){
					$weekends = 'false';
				}else{
					$weekends = "'".implode(',', $show_weekends)."'";
				}
				$noo_hidden = ( $show_cate_filter == 'no' || $source == 'both' ) ? 'noo-hidden' : '';

				?>
                <div class="noo-class-schedule">
                    <div id="<?php echo esc_attr($filter_id); ?>" class="class-schedule-filter noo-filters <?php echo esc_attr( $noo_hidden ); ?>">
						<?php if($source == 'both') { ?>
                            <input type="hidden" id="<?php echo $js_data_from ?>" value="<?php echo esc_attr($from_date); ?>" />
                            <input type="hidden" id="<?php echo $js_data_to ?>" value="<?php echo esc_attr($to_date); ?>">
						<?php } ?>
						<?php if( $filter_list ):?>
							<?php
							$data_filter_explode = array();
							if ( $data_filter_ids !== 'all' && $data_filter_ids !== '' ) {
								$data_filter_explode = explode(',', $data_filter_ids);
							}
							?>
							<?php
							if($show_filter == 'yes'){
								if($filter_layout == 'list')
								{
									?>
	                                <ul>
										<?php if ($show_all_tab == 'yes'): ?>
	                                        <li>
	                                            <div class="noo-class-schedule-icon" style="background-color:#795548" ></div>
	                                            <a href="#" class="selected" data-filter="<?php echo esc_attr($data_filter_ids); ?>"
	                                               data-from="<?php echo esc_attr($from_date); ?>" data-to="<?php echo esc_attr($to_date); ?>">
													<?php esc_html_e('Tất cả các lớp', 'noo-timetable') ?>
	                                            </a>
	                                        </li>
										<?php endif; ?>
										<?php foreach ((array) $filter_list as $fl): ?>
											<?php if (in_array($fl->id, $data_filter_explode) || $data_filter_ids === 'all') : ?>
                                                <?php
                                                    $class_type = $fl->title;
                                                    $icon_color = '';
                                                    switch ($class_type) {
                                                        case 'Lớp Knowledge':
                                                            $icon_color = '#052FFF';
                                                            break;
                                                        case 'Lớp Social Hour':
                                                            $icon_color = '#16A34A';
                                                            break;
                                                        case 'Lớp Soft Skills':
                                                            $icon_color = '#FEC200';
                                                            break;
                                                        default:
                                                            $icon_color = '#795548';
                                                    }
                                                ?>
	                                            <li>
	                                                <div class="noo-class-schedule-icon" id="test" style="background-color: <?php echo esc_attr($icon_color); ?>"></div>
	                                                <a href="#" data-filter="<?php echo esc_attr($fl->id) ?>"
	                                                   data-from="<?php echo esc_attr($from_date); ?>" data-to="<?php echo esc_attr($to_date); ?>">
														<?php echo esc_html($fl->title) ?>
	                                                </a>
	                                            </li>

											<?php endif; ?>
										<?php endforeach; ?>
										
							
                                            <!--jQuery(document).ready(function($) {-->
                                            <!--    $("#noo_class_schedule_659ebf311ce21").fullCalendar({-->
                                        
                                            <!--        eventRender: function(event, element, view) {-->
                                            <!--            if (event.catColor) {-->
                                            <!--                 console.log('Event catColor:', event.catColor);-->
                                            <!--                element.find('#test').css('background-color', event.catColor);-->
                                            <!--            }-->
                                            <!--        }-->
                                            <!--    });-->
                                            <!--});-->
                                      
	                                </ul>
									<?php
								} else { ?>
	                                <select>
										<?php if ($show_all_tab == 'yes'): ?>
	                                        <option value="<?php echo esc_attr($data_filter_ids); ?>" data-filter="<?php echo esc_attr($data_filter_ids); ?>"
	                                                data-from="<?php echo esc_attr($from_date); ?>" data-to="<?php echo esc_attr($to_date); ?>">
												<?php esc_html_e('All Category', 'noo-timetable') ?>
	                                        </option>
										<?php endif; ?>
										<?php foreach ((array) $filter_list as $fl): ?>
											<?php if (in_array($fl->id, $data_filter_explode) || $data_filter_ids === 'all') : ?>
	                                            <option value="<?php echo esc_attr($fl->id) ?>" data-filter="<?php echo esc_attr($fl->id) ?>"
	                                                    data-from="<?php echo esc_attr($from_date); ?>" data-to="<?php echo esc_attr($to_date); ?>">
													<?php echo esc_html($fl->title) ?>
	                                            </option>
											<?php endif; ?>
										<?php endforeach; ?>
	                                </select>
									<?php
								}
							}
							?>
						<?php endif;?>
                    </div>
                    <div id="<?php echo esc_attr($id)?>" class="class-schedule"></div>

					<?php
					if ( $show_export == 'yes' ) {
						echo '<div class="export-timetable"><a href="#" id="' . esc_attr($export_id) . '">' . esc_html__('Export', 'noo-timetable') . '</a></div>';
					}
					?>

                    <script>
                        var source_<?php echo esc_attr($id)?> = <?php echo json_encode($classes_arr); ?>;
                        var column_source_<?php echo esc_attr($id)?> = <?php echo json_encode($filter_list)?>;
                        var holiday_<?php echo esc_attr($id)?> = <?php echo json_encode($holidays)?>;
                    </script>
                     <!-- CODE TEST -->
                    <div id="no-events-image" style="display: none;">
                        <!-- anh them -->
                        <img src="https://zestforenglish.vn/wp-content/uploads/2023/11/couser-image-left.png" alt="TEST">
                    </div>
                     <!-- END CODE TEST -->
                    <script>
                        jQuery(document).ready(function($) {
                            
                            $("#<?php echo esc_attr($id)?>").fullCalendar({
                                isRTL: <?php echo is_rtl() ? 'true' : 'false'; ?>,
								<?php /*if ( $default_view == 'agendaDay' || $default_view == 'agendaWeek' ) : */?>
                                slotEventOverlap: true,
								<?php /*endif; */?>
								<?php if ( 'yes' == $general_header_toolbar ) : ?>
                                header: {
                                    left: '<?php echo $header['prev']; ?>',
                                    center: 'title',
                                    right: '<?php echo $header['next']; ?>',
                                },
								<?php else : ?>
                                header: false,
								<?php endif; ?>
                                slotLabelFormat: 'HH:mm',
                                minTime: '<?php echo apply_filters('noo-class-schedule-mintime', $min_time)?>',
                                maxTime: '<?php echo apply_filters('noo-class-schedule-maxtime', $max_time)?>',
                                timeFormat: "<?php echo $time_format; ?>",
                                slotLabelFormat: "<?php echo $time_format; ?>",
                                defaultView: '<?php echo esc_attr( $view ); ?>',
                                firstDay: <?php echo get_option('start_of_week'); ?>,
                                slotDuration: '01:00:00',
								<?php if ( $default_view == 'agendaWeek' && 'yes' == $general_header_day ) : ?>
                                columnHeaderFormat : "ddd <?php echo $date_format; ?>",
								<?php else : ?>
                                columnHeaderFormat : 'dddd',
								<?php endif; ?>
                                allDaySlot: false,
                                defaultDate: '<?php echo date('Y-m-d', $default_date); ?>',
                                editable: false,
                                locale:'<?php echo get_locale()?>',
                                eventLimit: <?php echo $item_limit; ?>, // allow "more" link when too many events
                                events: source_<?php echo esc_attr($id)?>,
                                resources: column_source_<?php echo esc_attr($id)?>,
                                labelColumnTime: '<?php esc_html_e('Time', 'noo-timetable') ?>',
								<?php if($weekends): ?>
                                weekends: <?php echo $weekends;?>,
								<?php else: ?>
                                weekends: false,
								<?php endif;?>
                                eventLimitText: '<?php echo esc_html__('more','noo-timetable'); ?>',
								<?php if ( $general_popup == 'yes' ) : ?>
                                dataModal: 'modal-<?php echo $popup_id; ?>',
								<?php endif; ?>
								<?php if ( $source == 'event' ) : ?>
                                textWith: '<span><?php echo esc_html__('at','noo-timetable'); ?></span>',
								<?php else : ?>
                                textWith: '<span><?php echo esc_html__('with','noo-timetable'); ?></span>',
								<?php endif; ?>
                                textLevel:  '<span><?php echo esc_html__('level','noo-timetable'); ?></span>',
                                displayEventEnd: true,
								<?php if ( $default_view != 'month'  ) : ?>
                                hideTimeRange: '<?php echo $hide_time_range; ?>',
								<?php endif; ?>
                                contentHeight: <?php echo $content_height; ?>,
                                height: <?php echo $content_height; ?>,
								<?php if ( $source == 'class' ) : ?>
                                eventDataTransform: false,
								<?php endif; ?>

                                eventRender: function(event, element, view) {
                                    var $schedule_layout = '<?php echo $schedule_layout; ?>';
                                    //thiết lập chỉnh màu cho title
                                    var titleHtml = '<div class="fc-title" style="color:' + event.catColor + '">' + event.title + '</div>';
                                    var timeHtml = '<div class="fc-time" style="color:' + event.catColor + '">' + event.time + '</div>';
                                    var modalElement = document.querySelector('.md-modal');
   
                                     // check qua han
                                        if (event.end && event.end.isBefore(moment())) {
                                            //  console.log('Event:', event);
                                            element.addClass('event-past');
                                            element.find('a').contents().unwrap();
                                            // element.removeClass('md-modal md-effect-1 md-modal-init md-show');
                                            //     if (modalElement) {
                                            //         modalElement.style.display = 'none';
                                            //     }else {
                                            //         var newElement = document.createElement('div');
                                            //         newElement.className = 'md-show';
                                            //         document.body.appendChild(newElement);
                                            // }
                                            element.off('click');
                                          
                                            element.find('.fc-content').css({
                                                'background-color': '#FFF',
                                                'opacity': 0.4,
                                                'cursor': 'not-allowed'
                                            });
                                            element.find('.fc-title').css('color', '#6B7280');
                                            element.find('.fc-trainer').css('color', '#6B7280');
                                            element.find('.fc-category').css('color', '#6B7280');
                                            
                                        element.on('click', function(e) {
                                            e.preventDefault();
                                            e.stopPropagation();
                                        });
                                        
                                        }else{
                                            // modalElement.style.display = 'none';
                                            element.on('click', function() {
                                                showModalDetail(event);
                                            });
                                            
                                        
                                        }
                                        
                                        function checkAndDisplayImage() {
                                            // Lấy thời gian của tuần hiện tại
                                            var currentView = $("#<?php echo esc_attr($id)?>").fullCalendar('getView');
                                            var startOfWeek = currentView.start;
                                            var endOfWeek = currentView.end;
                                    
                                            // Lấy tất cả sự kiện trong khoảng thời gian của tuần
                                            var eventsInWeek = $("#<?php echo esc_attr($id)?>").fullCalendar('clientEvents', function(event) {
                                                return event.start.isBetween(startOfWeek, endOfWeek, null, '[]') ||
                                                    event.end.isBetween(startOfWeek, endOfWeek, null, '[]');
                                            });
                                    
                                            
                                            if (eventsInWeek.length === 0) {
                                                
                                                $('#<?php echo esc_attr($id)?>').find('img').remove();
                                                
                                                var imageUrl = 'https://zestforenglish.vn/wp-content/uploads/2024/01/no-event-zestforenglish.png';
                                                $("#<?php echo esc_attr($id)?>").find('table').css({
                                                    'background': 'url(' + imageUrl + ')',
                                                    'background-repeat': 'no-repeat',
                                                     'width': '100%'
                                                });
                                            }
                                        }
                                        $('.fc-prev-button').on('click', function() {
                                           
                                            checkAndDisplayImage();
                                            
                                        });
                                    
                                        // check trang khi load img
                                        checkAndDisplayImage();

                                    
                                    <?php if(apply_filters('shortcode_ntt_schedule_open_item_as_modal',true)): ?>

                                    element.attr('data-modal', 'modal-<?php echo $popup_id; ?>');

                                	<?php endif; ?>

                                    if(event.catColor){
										<?php if( $schedule_layout == 'grid' ) { ?>
                                        element.append('<div class="fc-ribbon" style="background-color:' + event.catColor + '"></div>');
										<?php } else { ?>
                                        element.find('.fc-event-dot').css('background-color', event.catColor);
										<?php } ?>
                                    }
                                    
                                    if(event.time) {
                                        element.find('.fc-time').html(timeHtml);
                                    }

                                    if(event.title) {
                                        element.find('.fc-title').html(titleHtml);
                                        //   element.find('.fc-title').html(event.title);
                                        // element.find('.fc-title').append('<div class="fc-title" style="color:' + event.catColor + '">' + htmlEscape(event.title) + '</div>');
                                    }
                                  
                                    <?php if($schedule_layout == 'list'){?>
	                                    <?php if('yes' == $teacher_of_class){?>
	                                    	if(event.trainer) {
		                                        element.find('.fc-list-item-title').append('<span class="fc-trainer">' + '<span class="fc-with"> - with </span>' + event.trainer + '</span>');
		                                    }
		                                <?php }?>
		                                <?php if('yes' == $address_of_class){?>
			                                if(event.address) {
		                                        element.find('.fc-list-item-title').append('<span class="fc-address">'  + ' - ' + event.address + '</span>');
		                                    }
		                                <?php }?>
                                    <?php }?>
									<?php if($default_view != 'month') { ?>
	                                    if(event.backgroundImage != null && $schedule_layout == 'grid'){
	                                        element.find('.fc-bg').css('background-image', 'url('+event.backgroundImage+')');
	                                    }
	                                    
	                                    if(event.trainer) {
	                                        element.find('.fc-content').append('<div class="fc-trainer">' + event.trainer + '</div>');
	                                    }
	                                    if(event.showcatBycolor == 'yes' && event.categoryName) {
	                                        element.find('.fc-content').append('<div class="fc-category" style="background-color:' + event.catColor + '">' + htmlEscape(event.categoryName) + '</div>');
	                                    }	
									<?php } ?>
                                    var timeHtml = '';
                                    var timeText = '';
                                    if (timeText) {
                                        timeHtml = '<span class="fc-time">' + htmlEscape(timeText) + '</span>';
                                    }

                                    /** Modal when click **/
                                    modalInfo = {};
                                    modalInfo['title'] = (event.title);
									<?php if ( $source == 'event' ) : ?>
                                    var textWith = '<?php echo esc_html__('at','noo-timetable'); ?></span>';
									<?php else : ?>
                                    var textWith = '<?php echo esc_html__('with','noo-timetable'); ?>';
									<?php endif; ?>
                                    var textLevel = '<?php echo esc_html__('level','noo-timetable'); ?>';
                                    if(event.trainer) {
                                        modalInfo['trainer'] = textWith + ' ' + event.trainer;
                                    }
                                    if(event.level) {
                                        modalInfo['level'] = textLevel + ' ' + event.level;
                                    }
                                    modalInfo['time'] = timeHtml;

                                    modalInfo['categoryName'] 	 = event.categoryName === undefined ? '' : event.categoryName;
                                    modalInfo['backgroundColor'] = event.backgroundColor === undefined ? '' : htmlEscape(event.backgroundColor);
                                    modalInfo['catColor'] 		 = event.catColor === undefined ? '' : htmlEscape(event.catColor);
                                    modalInfo['popup_bgImage'] 	 = event.popup_bgImage;
                                    modalInfo['url'] = htmlEscape(event.url);
                                    modalInfo['excerpt'] = event.excerpt; /* Remove htmlEscape*/
                                    if(event.address != '') {
                                        modalInfo['address'] = htmlEscape(event.address);
                                    }
                                    modalInfo['register_link'] = event.register === undefined ? '' : htmlEscape(event.register);
                                    element.append('<input type="hidden" value=\'' + JSON.stringify(modalInfo).replace(/'/g,"&apos;") + '\'/>');
                                },
                                eventAfterAllRender: function( view ) {
                                    
                                    if ( jQuery('.noo-class-schedule-shortcode').width() <= 950 ) {
                                        jQuery('.noo-class-schedule-shortcode').addClass('small-view');
                                    }
                                    /** Style holiday in month view **/
                                    // holidayRestyle();
                                    var holidays = holiday_<?php echo esc_attr($id)?>;
                                    var el = view.el;
                                    var holidayMoment;
                                    for(var i = 0; i < holidays.length; i++) {
                                        holidayMoment = moment(holidays[i]['day'],'YYYY-MM-DD');
                                        var aDay = el.find('.fc-bg table td[data-date=' + holidayMoment.format("YYYY-MM-DD") + ']');

                                        if(aDay.hasClass('fc-day')) {
                                            aDay.addClass(holidays[i]['className']);
                                            aDay.css(
                                                { 'background-color': holidays[i]['backgroundColor'] }
                                            );

                                            if(aDay.hasClass('fc-noo-class-holiday')){
                                            	aDay.html('<span class="fc-content">'  + ' - ' + holidays[i]['description'] + '</span>');
                                            }
                                        }

                                    }
                                    /** End Style holiday **/
                                    ModalEffectsInit();
									<?php if( $schedule_layout == 'list' ) { ?>
                                    setTodayListView();
									<?php } ?>
                                }
                            });

                            $('.fc-prev-button').attr('data-from', '<?php echo $prev_from; ?>').attr('data-to', '<?php echo $prev_to; ?>');
                            $('.fc-next-button').attr('data-from', '<?php echo $next_from; ?>').attr('data-to', '<?php echo $next_to; ?>');

                            var schedule_layout = '<?php echo $schedule_layout; ?>';
                            var fc_body = '.fc-body';
                            if(schedule_layout == 'list') {
                                fc_body = '.fc-list-table';
                            }
							<?php if($filter_layout == 'list') { ?>
                            // Filter Active in first load
                            $(".noo-class-schedule #<?php echo esc_attr($filter_id); ?> ul li").first().find('a').addClass('selected');

                            $(".noo-class-schedule #<?php echo esc_attr($filter_id); ?> a").on("click", function(e){
                                e.preventDefault();
                                var $this = $(this);

                                $.ajax({
                                    type: 'POST',
                                    url: nooTimetableParams.ajax_url,
                                    data: {
                                        action          : '<?php echo $action_filter; ?>',
										<?php if( $source !== 'both' ) { ?>
                                        class_category  : $this.data("filter"),
										<?php } else { ?>
                                        class_category  : 'all',
										<?php } ?>
										<?php if( $source == 'class' ) { ?>
                                        filter_type: '<?php echo $filter_type; ?>',
										<?php } ?>
										<?php if( $source !== 'both' ) { ?>
                                        from: $this.attr('data-from'),
                                        to: $this.attr('data-to'),
										<?php } ?>
                                        shorcode_attr   : '<?php echo json_encode($new_atts); ?>',
                                        sercurity       : '<?php echo wp_create_nonce( 'class_filter' ); ?>'
                                    },
                                    beforeSend: function() {
                                        $this.closest('.noo-class-schedule').find(fc_body).addClass('overlay-loading-tripped');
                                        $this.closest('.noo-class-schedule').find(".class-schedule-filter a.selected")
                                            .removeClass("selected")
                                            .removeClass('class-schedule-infi-pulse');
                                        $this
                                            .addClass("selected")
                                            .addClass('class-schedule-infi-pulse');
                                    },
                                    success: function(res){
                                        var newsource = res.events_data;
                                        $this.closest('.noo-class-schedule').find(fc_body).removeClass('overlay-loading-tripped');
                                        $this.closest('.noo-class-schedule').find(".class-schedule-filter a.selected")
                                            .removeClass("selected")
                                            .removeClass('class-schedule-infi-pulse');
                                        $this
                                            .addClass("selected")
                                            .removeClass('class-schedule-infi-pulse');

                                        if(newsource){
                                            $("#<?php echo esc_attr($id)?>").fullCalendar('removeEventSource', source_<?php echo esc_attr($id)?>)
                                            $("#<?php echo esc_attr($id)?>").fullCalendar('refetchEvents');
                                            $("#<?php echo esc_attr($id)?>").fullCalendar('addEventSource', newsource);
                                            $("#<?php echo esc_attr($id)?>").fullCalendar('refetchEvents');
                                            source_<?php echo esc_attr($id)?> = newsource;
                                        }
                                    },
                                    error: function () {
                                        location.reload();
                                    }
                                });
                            });

							<?php } else { ?>
                            $(".noo-class-schedule #<?php echo esc_attr($filter_id); ?> select option").first().attr("selected", "selected");
                            $(".noo-class-schedule #<?php echo esc_attr($filter_id); ?> select").on("change", function(e){
                                e.preventDefault();
                                var $this = $(this);
                                var $curOptData = $this.find(':selected');

                                $.ajax({
                                    type: 'POST',
                                    url: nooTimetableParams.ajax_url,
                                    data: {
                                        action          : '<?php echo $action_filter; ?>',
										<?php if( $source !== 'both' ) { ?>
                                        class_category  : $curOptData.data("filter"),
										<?php } else { ?>
                                        class_category  : 'all',
										<?php } ?>
										<?php if( $source == 'class' ) { ?>
                                        filter_type: '<?php echo $filter_type; ?>',
										<?php } ?>
										<?php if( $source !== 'both' ) { ?>
                                        from: $curOptData.attr('data-from'),
                                        to: $curOptData.attr('data-to'),
										<?php } ?>
                                        shorcode_attr   : '<?php echo json_encode($new_atts); ?>',
                                        sercurity       : '<?php echo wp_create_nonce( 'class_filter' ); ?>'
                                    },
                                    beforeSend: function() {
                                        $this.closest('.noo-class-schedule').find(fc_body).addClass('overlay-loading-tripped');
                                        $this.closest('.noo-class-schedule').find(".class-schedule-filter a.selected")
                                            .removeClass("selected")
                                            .removeClass('class-schedule-infi-pulse');
                                        $this
                                            .addClass("selected")
                                            .addClass('class-schedule-infi-pulse');
                                    },
                                    success: function(res){
                                        var newsource = res.events_data;
                                        $this.closest('.noo-class-schedule').find(fc_body).removeClass('overlay-loading-tripped');
                                        $this.closest('.noo-class-schedule').find(".class-schedule-filter a.selected")
                                            .removeClass("selected")
                                            .removeClass('class-schedule-infi-pulse');
                                        $this
                                            .addClass("selected")
                                            .removeClass('class-schedule-infi-pulse');

                                        if(newsource){
                                            $("#<?php echo esc_attr($id)?>").fullCalendar('removeEventSource', source_<?php echo esc_attr($id)?>)
                                            $("#<?php echo esc_attr($id)?>").fullCalendar('refetchEvents')
                                            $("#<?php echo esc_attr($id)?>").fullCalendar('addEventSource', newsource)
                                            $("#<?php echo esc_attr($id)?>").fullCalendar('refetchEvents');
                                            source_<?php echo esc_attr($id)?> = newsource;
                                        }
                                    },
                                    error: function () {
                                        location.reload();
                                    }
                                });
                            });
							<?php } ?>
							<?php if($source != 'event'){?>
	                            $('body').on("click", ".noo-class-schedule #<?php echo esc_attr($id)?> .fc-toolbar .fc-prev-button, .noo-class-schedule #<?php echo esc_attr($id)?> .fc-toolbar .fc-next-button", function(e){
	                                e.preventDefault();
	                                var $this = $(this);

	                                $this.prop('disabled', true);

									<?php if($filter_layout == 'list') { ?>
	                                var filterObj = $this.parents('.noo-class-schedule').find('.noo-filters');
	                                var filterSelected = filterObj.find('a.selected');
									<?php } else { ?>
	                                var filterObj = $this.parents('.noo-class-schedule').find('.noo-filters').find('select');
	                                var filterSelected = filterObj.find(':selected');
									<?php } ?>
	                                $.ajax({
	                                    type: 'POST',
	                                    url: nooTimetableParams.ajax_url,
	                                    data: {
	                                        action          : '<?php echo $action_filter; ?>',
											<?php if( $source !== 'both' ) { ?>
	                                        class_category  : filterSelected.data("filter"),
											<?php } else { ?>
	                                        class_category  : 'all',
											<?php } ?>
	                                        from: $this.attr('data-from'),
	                                        to: $this.attr('data-to'),
											<?php if( $source === 'both' ) { ?>
	                                        filter_type: '<?php echo $filter_type; ?>',
											<?php } ?>
	                                        shorcode_attr   : '<?php echo json_encode($new_atts); ?>',
	                                        sercurity       : '<?php echo wp_create_nonce( 'class_filter' ); ?>'
	                                    },
	                                    beforeSend: function() {
	                                        $this.closest('.noo-class-schedule').find(fc_body).addClass('overlay-loading-tripped');
	                                    },
	                                    success: function(res){
	                                        var newsource = res.events_data;
	                                        var sche_wrap = $this.closest('.noo-class-schedule');
	                                        sche_wrap.find(fc_body).removeClass('overlay-loading-tripped');
	                                        if(newsource){
	                                            $("#<?php echo esc_attr($id)?>").fullCalendar('removeEventSource', source_<?php echo esc_attr($id)?>)
	                                            $("#<?php echo esc_attr($id)?>").fullCalendar('refetchEvents')
	                                            $("#<?php echo esc_attr($id)?>").fullCalendar('addEventSource', newsource)
	                                            $("#<?php echo esc_attr($id)?>").fullCalendar('refetchEvents');
	                                            source_<?php echo esc_attr($id)?> = newsource;
	                                        }

											<?php if($source == 'both') { ?>
	                                        $('#<?php echo $js_data_from ?>').val(res.datetime.cur_from);
	                                        $('#<?php echo $js_data_to ?>').val(res.datetime.cur_to);
											<?php } ?>
											<?php if($filter_layout == 'list') { ?>
	                                        filterObj.find('a').attr( 'data-from', res.datetime.cur_from);
	                                        filterObj.find('a').attr( 'data-to', res.datetime.cur_to);
											<?php } else { ?>
	                                        filterObj.find('option').attr( 'data-from', res.datetime.cur_from);
	                                        filterObj.find('option').attr( 'data-to', res.datetime.cur_to);
											<?php } ?>

	                                        //sche_wrap.find('.res-sche-navigation h3').html(label_start + ' - ' + label_end);

	                                        var _nav_prev = sche_wrap.find('.fc-toolbar .fc-prev-button');
	                                        var _nav_next = sche_wrap.find('.fc-toolbar .fc-next-button');

	                                        _nav_prev.attr( 'data-from', res.datetime.prev_from );
	                                        _nav_prev.attr( 'data-to', res.datetime.prev_to );

	                                        _nav_next.attr( 'data-from', res.datetime.next_from );
	                                        _nav_next.attr( 'data-to', res.datetime.next_to );

	                                        /** Style holiday in month view **/
	                                            //holidayRestyle();
	                                        var holidays = res.holidays_data;
	                                        var el = $('.fc-<?php echo $default_view; ?>-view');
	                                        var holidayMoment;
	                                        for(var i = 0; i < holidays.length; i++) {
	                                            holidayMoment = moment(holidays[i]['day'],'YYYY-MM-DD');
	                                            var aDay = el.find('.fc-bg table td[data-date=' + holidayMoment.format("YYYY-MM-DD") + ']');
	                                            if(aDay.hasClass('fc-day')) {
	                                                aDay.addClass(holidays[i]['className']);
	                                                aDay.css(
	                                                    { 'background-color': holidays[i]['backgroundColor'] }
	                                                );
	                                                if(aDay.hasClass('fc-noo-class-holiday'))
	                                                	aDay.html('<span class="fc-content">'  + ' - ' + holidays[i]['description'] + '</span>');
	                                            }
	                                        }
	                                        
	                                        $this.prop('disabled', false);
	                                        /** End Style holiday **/
	                                    },
	                                    error: function () {
	                                        //location.reload();
	                                    }
	                                });
	                            });
							<?php }?>
                            $( window ).resize(function() {
                                var $sch_view = $("#<?php echo esc_attr($id)?>").find('.fc-view');
                                if ( !$.trim( $sch_view.html() ) ) {
                                    $("#<?php echo esc_attr($id)?>").find('.fc-toolbar').find('.fc-prev-button').click();
                                    $("#<?php echo esc_attr($id)?>").find('.fc-toolbar').find('.fc-next-button').click();
                                }
                            });


                            // Download iCal button onclick listener
                            $("#<?php echo esc_attr($export_id); ?>").on('click',function(){
                                // setup ics
                                var cal = ics();
                                // go through each event from the json and add an event for it to ics
                                $.each(source_<?php echo esc_attr($id)?>,function(i, $event){
									<?php if ($source === 'event') : ?>
                                    var _desc     =  '';
                                    var _location =  $event.trainer;
									<?php else : ?>
                                    var _desc     =  $event.categoryName;
                                    var _location =  $event.address;
									<?php endif; ?>
                                    cal.addEvent($event.title, _desc, _location, $event.start, $event.end, $event.url);
                                });

                                cal.download('<?php esc_html_e('ical-class-chedule', 'noo-timetable'); ?>', '.ics');
                                return false;
                            });

                            $('.md-modal').addClass('md-modal-init');
                        });
                    </script>
                </div>


            </div> <!-- /.noo-class-schedule-shortcode -->
			<div class="noo-responsive-schedule-wrap">
				<?php
				if ( get_option('start_of_week') == date( "w", $default_date ) ) {
					$first_week_day = date('Y-m-d', $default_date);
				} else {
					$start_of_week = Noo__Timetable__Class::_get_week_day( get_option('start_of_week') );
					$first_week_day = date( 'Y-m-d', strtotime('last ' . $start_of_week, $default_date) );
				}
				$end_week_day = date( 'Y-m-d', strtotime($first_week_day . ' +6 days') );
				//Create label
				$label_start = date_i18n( get_option( 'date_format' ), strtotime($first_week_day) );
				$label_end = date_i18n( get_option( 'date_format' ), strtotime($first_week_day . ' +6 days') );
				// Current
				$curr_start = $first_week_day;
				$curr_end = date('Y-m-d', strtotime($first_week_day . ' +6 days') );
				// Create nav
				$prev_from = date('Y-m-d',( strtotime ( '-1 week' , strtotime ( $first_week_day ) ) ) );
				$prev_to = date('Y-m-d',( strtotime ( '-1 week' , strtotime ( $end_week_day ) ) ) );

				$next_from = date('Y-m-d',( strtotime ( '+1 week' , strtotime ( $first_week_day ) ) ) );
				$next_to = date('Y-m-d',( strtotime ( '+1 week' , strtotime ( $end_week_day ) ) ) );
				?>
				<input type="hidden" name="cat_selected" value="<?php echo $data_filter_ids;?>">
				<?php if('yes' == $show_category):?>
	                <div id="<?php echo esc_attr($filter_id); ?>" class="noo-filters filter-mobile <?php echo esc_attr( $noo_hidden ); ?>">
						<?php if( $filter_list ):?>
							<?php
							$data_filter_explode = array();
							if ( $data_filter_ids !== 'all' && $data_filter_ids !== '' ) {
								$data_filter_explode = explode(',', $data_filter_ids);
							}
							?>
	                        <select class="filter-mb-dropdown">
								<?php if ($show_all_tab == 'yes'): ?>
	                                <option value="<?php echo esc_attr($data_filter_ids); ?>" data-filter="<?php echo esc_attr($data_filter_ids); ?>"
	                                        data-from="<?php echo esc_attr($curr_start); ?>" data-to="<?php echo esc_attr($curr_end); ?>">
										<?php esc_html_e('Tất cả các lớp', 'noo-timetable') ?>
	                                </option>
								<?php endif; ?>
								<?php foreach ((array) $filter_list as $fl): ?>
									<?php if (in_array($fl->id, $data_filter_explode) || $data_filter_ids === 'all') : ?>
	                                    <option value="<?php echo esc_attr($fl->id) ?>" data-filter="<?php echo esc_attr($fl->id) ?>"
	                                            data-from="<?php echo esc_attr($curr_start); ?>" data-to="<?php echo esc_attr($curr_end); ?>">
											<?php echo esc_html($fl->title) ?>
	                                    </option>
									<?php endif; ?>
								<?php endforeach; ?>
	                        </select>
						<?php endif;?>
	                </div>
				<?php endif;?>
                <div class="res-sche-navigation">
                    <button class="prev" data-from="<?php echo esc_attr( $prev_from ); ?>" data-to="<?php echo esc_attr( $prev_to ); ?>"><i class="fa fa-chevron-left" aria-hidden="true"></i></button>
                    <h3><?php echo esc_attr( $label_start ); ?> - <?php echo esc_attr( $label_end ); ?></h3>
                    <button class="next" data-from="<?php echo esc_attr( $next_from ); ?>" data-to="<?php echo esc_attr( $next_to ); ?>"><i class="fa fa-chevron-right" aria-hidden="true"></i></button>
                </div>
                <div class="res-sche-content">
					<?php
					if ( $source == 'event' ) {?>
                        <h2><?php echo esc_html_e('Event','noo-timetable')?></h2>
						<?php
						Noo__Timetable__Event::show_list_calender_mobile( $first_week_day, $end_week_day, $data_filter_ids, $new_atts );
					} elseif($source == 'both') {?>
                        <h2><?php echo esc_html_e('Class','noo-timetable')?></h2>
						<?php
						$doituong = new Noo__Timetable__Class();
						$doituong->_schedule_class_list_mobile($first_week_day, $end_week_day, $data_filter_ids, $new_atts);?>
                        <h2><?php echo esc_html_e('Event','noo-timetable')?></h2>
						<?php   Noo__Timetable__Event::show_list_calender_mobile( $first_week_day, $end_week_day, $data_filter_ids, $new_atts );
					}else{?>
                        <h2><?php echo esc_html_e('Class','noo-timetable')?></h2>
						<?php
						$doituong = new Noo__Timetable__Class();
						$doituong->_schedule_class_list_mobile($first_week_day, $end_week_day, $data_filter_ids, $new_atts);
					}

					?>
                </div>
                <div class="res-sche-navigation">
                    <button class="prev" data-from="<?php echo esc_attr( $prev_from ); ?>" data-to="<?php echo esc_attr( $prev_to ); ?>"><i class="fa fa-chevron-left" aria-hidden="true"></i></button>
                    <h3><?php echo esc_attr( $label_start ); ?> - <?php echo esc_attr( $label_end ); ?></h3>
                    <button class="next" data-from="<?php echo esc_attr( $next_from ); ?>" data-to="<?php echo esc_attr( $next_to ); ?>"><i class="fa fa-chevron-right" aria-hidden="true"></i></button>
                </div>

            </div> <!-- noo-responsive-schedule-wrap -->
           

        </div>
       <script>
            jQuery(document).ready(function($) {
                $(".noo-responsive-schedule-wrap #<?php echo esc_attr($filter_id); ?> ul li").first().find('a').addClass('selected');
                $(".res-sche-navigation button").on("click", function(e){
                    e.preventDefault();
                    var $this = $(this);
                    $this.prop('disabled', true);

                    var $filterObj = $(".noo-responsive-schedule-wrap").find('input[name="cat_selected"]');
                    $.ajax({
                        type: 'POST',
                        url: nooTimetableParams.ajax_url,
                        data: {
                            action          : '<?php echo $action_mobile_filter; ?>',
                            from            : $this.attr("data-from"),
                            to              : $this.attr("data-to"),
							<?php if( $source !== 'both' ) { ?>
                            the_category    : $filterObj.val(),
							<?php } else { ?>
                            the_category    : 'all',
							<?php } ?>
							<?php if( $source == 'class' || $source == 'both' ) { ?>
                            filter_type: '<?php echo $filter_type; ?>',
							<?php } ?>
                            weekends        : true,
                            shorcode_attr   : '<?php echo json_encode($new_atts); ?>',
                            sercurity       : '<?php echo wp_create_nonce( 'class_responsive_navigation' ); ?>'
                        },
                        beforeSend: function() {
                            var sche_wrap = $this.closest('.noo-responsive-schedule-wrap');
                            sche_wrap.find('.res-sche-content').addClass('overlay-loading-tripped');
                        },
                        success: function(res){

                            var sche_wrap = $this.closest('.noo-responsive-schedule-wrap');
                            sche_wrap.find('.res-sche-content').removeClass('overlay-loading-tripped');
                            sche_wrap.find('.res-sche-content').html(res);

                            label_start = sche_wrap.find('.label-start').val();
                            label_end = sche_wrap.find('.label-end').val();

                            curr_start = sche_wrap.find('.curr-start').val();
                            curr_end = sche_wrap.find('.curr-end').val();
                            $(".filter-mobile select option").attr( 'data-from', curr_start);
                            $(".filter-mobile select option").attr( 'data-to', curr_end);

                            sche_wrap.find('.res-sche-navigation h3').html(label_start + ' - ' + label_end);

                            var _nav_prev = sche_wrap.find('.res-sche-navigation .prev');
                            var _nav_next = sche_wrap.find('.res-sche-navigation .next');

                            _nav_prev.attr( 'data-from', sche_wrap.find('.prev-from-hidden').val() );
                            _nav_prev.attr( 'data-to', sche_wrap.find('.prev-to-hidden').val() );

                            _nav_next.attr( 'data-from', sche_wrap.find('.next-from-hidden').val() );
                            _nav_next.attr( 'data-to', sche_wrap.find('.next-to-hidden').val() );

                            $this.prop('disabled', false);

                        },
                        error: function () {
                            location.reload();
                        }
                    });
                });

                $(".filter-mobile select").on("change", function(e){
                    e.preventDefault();
                    var $this = $(this);
                    var curOptData = $(this).find(':selected');

                    $.ajax({
                        type: 'POST',
                        url: nooTimetableParams.ajax_url,
                        data: {
                            action          : '<?php echo $action_mobile_filter; ?>',
                            from            : curOptData.attr("data-from"),
                            to              : curOptData.attr("data-to"),
                            the_category    : curOptData.data("filter"),
                            weekends        : true,
                            shorcode_attr   : '<?php echo json_encode($new_atts); ?>',
                            sercurity       : '<?php echo wp_create_nonce( 'class_responsive_navigation' ); ?>'
                        },
                        beforeSend: function() {
                            var sche_wrap = $this.closest('.noo-responsive-schedule-wrap');
                            sche_wrap.find('.res-sche-content').addClass('overlay-loading-tripped');
                        },
                        success: function(res){

                            var sche_wrap = $this.closest('.noo-responsive-schedule-wrap');
                            sche_wrap.find('.res-sche-content').removeClass('overlay-loading-tripped');
                            sche_wrap.find('.res-sche-content').html(res);

                            label_start = sche_wrap.find('.label-start').val();
                            label_end = sche_wrap.find('.label-end').val();

                            sche_wrap.find('.res-sche-navigation h3').html(label_start + ' - ' + label_end);

                            var _nav_prev = sche_wrap.find('.res-sche-navigation .prev');
                            var _nav_next = sche_wrap.find('.res-sche-navigation .next');

                            _nav_prev.attr( 'data-from', sche_wrap.find('.prev-from-hidden').val() );
                            _nav_prev.attr( 'data-to', sche_wrap.find('.prev-to-hidden').val() );

                            _nav_next.attr( 'data-from', sche_wrap.find('.next-from-hidden').val() );
                            _nav_next.attr( 'data-to', sche_wrap.find('.next-to-hidden').val() );

                        },
                        error: function () {
                            location.reload();
                        }
                    });
                });

            });

        </script>
       
		<?php if ( $general_popup == 'yes' ) :?>
            <div class="md-modal md-effect-<?php echo $general_popup_style ;?>" id="modal-<?php echo $popup_id; ?>">
                <div class="md-content">
                    <h3></h3>
                    <div class="div_content">
						<?php if('yes' == $general_popup_thumb):?>
                            <div class="fc-thumb"></div>
						<?php endif;?>
						<?php if('yes' == $general_popup_time):?>
                            <div class="fc-time"></div>
						<?php endif;?>
						<?php if('yes' == $general_popup_title):?>
                            <div class="fc-title"><a></a></div>
						<?php endif;?>
						<?php if('yes' == $general_popup_level):?>
                            <div class="fc-level"><a></a></div>
						<?php endif;?>
						<?php if('yes' == $general_popup_adress_trainer):?>
                            <div class="fc-trainer"></div>
						<?php endif;?>
						<?php if('yes' == $general_popup_excerpt):?>
                            <div class="fc-excerpt"></div>
						<?php endif;?>

						<?php //if ($source == 'class'): ?>
                        <div class="fc-register"><a><?php echo esc_html_e('Register', 'noo-timetable'); ?></a></div>
						<?php //endif; ?>
                        <div class="fc-address"></div>
                    </div>
                    <a class="md-close"></a>
                </div>
            </div>
            <div class="md-overlay"></div>

		<?php endif; ?>

		<?php
		if ( function_exists('noo_timetable_customizer_css_generator') ) {
			$options_design = array(
				'css_class'                 => $id,
				'general_header_background' => $general_header_background,
				'general_holiday_background' => $general_holiday_background,
				'general_header_color'      => $general_header_color,
				'general_today_column'      => $general_today_column,
                'content_height'            => $content_height,
			);
			noo_timetable_customizer_css_generator( $options_design );
		}
		wp_reset_postdata();
		?>

		<?php $html = ob_get_contents();
		ob_end_clean();
		return $html;

	}

	add_shortcode( 'ntt_schedule', 'shortcode_ntt_schedule' );

endif;
