<?php

add_shortcode('guidle-events-list', 'show_guidle_events_list');

function show_guidle_events_list($attrs) {
    if (is_list($attrs)) {
        return get_event_list(
            get_event_list_url($attrs),
            get_target_wp_page_url(($attrs))
        );
    }
    if(isset($_GET['id'])) {
        return get_event_details(
            get_event_details_base_url($attrs),
            $_GET['id'],
            get_event_details_language($attrs)
        );
    }
    return false;
}

function get_event_list_as_json($eventListUrl) {
    $request = wp_remote_get($eventListUrl);
    if( is_wp_error( $request ) ) {
        return false; 
    }
    $body = wp_remote_retrieve_body($request);    
    return json_decode($body, true);
}

function get_event_details_as_json($eventDetailsBaseUrl, $eventId, $language) {
    $request = wp_remote_get($eventDetailsBaseUrl . $eventId . "/" . $language);
    if( is_wp_error( $request ) ) {
        return false; 
    }
    $body = wp_remote_retrieve_body($request); 
    return json_decode($body, true);
}

function get_attributes($attrs) {
    $defaultAttributes = array(
        'event-list-url' => '',
        'target-wp-page-url' => '',
        'event-details-base-url' => '',
        'lang' => 'de'
    );
    return shortcode_atts($defaultAttributes, $attrs);
}

function is_list($attrs) {
    return !empty(get_attributes($attrs)['event-list-url']);
}

function get_event_list_url($attrs) {
    return get_attributes($attrs)['event-list-url'];
}

function get_event_details_base_url($attrs) {
    return get_attributes($attrs)['event-details-base-url'];
}

function get_event_details_language($attrs) {
    return get_attributes($attrs)['lang'];
}

function get_target_wp_page_url($attrs) {
    return get_attributes($attrs)['target-wp-page-url'];
}

function get_event_list($eventListUrl, $targetWPPageUrl) {
    $offerListJson = flatten_event_list(get_event_list_as_json($eventListUrl));
    $smarty = new Smarty();
    $template = GUIDLE_EVENTS_PLUGIN_PATH . 'includes/templates/event-list.html';
    $finalWPPageUrl = $targetWPPageUrl;
    if (str_contains($targetWPPageUrl, "?")) {
        $finalWPPageUrl .= '&id=';
    } else {
        $finalWPPageUrl .= '?id=';
    } 
    $smarty->assign('offers', $offerListJson);
    $smarty->assign('finalWPPageUrl', $finalWPPageUrl);
    return $smarty->fetch($template);
}

function get_event_details($eventDetailsBaseUrl, $eventId, $language) {
    if($eventId) {
        $offerDetailsJson = get_event_details_as_json($eventDetailsBaseUrl, $eventId, $language);
        $smarty = new Smarty();
        $template = GUIDLE_EVENTS_PLUGIN_PATH . 'includes/templates/event-details.html';
        $smarty->assign('offerDetails', $offerDetailsJson);
        return $smarty->fetch($template);
    }
    return false;
}

function flatten_event_list($original) {
    $flattened = [];
    if($original) {
        foreach($original['groupSet'] as $groupSet) {
            foreach($groupSet['offers'] as $offer) {
                foreach($offer['offerDetail'] as $offerDetail) {
                    $tmpOffer = array(
                        "id" => $offer['id'],
                        "date" => array(
                            "fullDate" => $groupSet['name'],
                            "day" => date_i18n('j', strtotime($groupSet['name'])),
                            "month" => date_i18n('F', strtotime($groupSet['name'])),
                            "weekDay" => date_i18n('l', strtotime($groupSet['name'])),
                        ),
                        "offerDetail" => $offerDetail,
                    );
                    $flattened[] = $tmpOffer;
                    break;
                }            
            }
        }
        return $flattened;
    }
    return false;
}

