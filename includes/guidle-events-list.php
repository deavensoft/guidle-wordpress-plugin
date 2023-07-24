<?php

add_shortcode('guidle-events-list', 'show_guidle_events_list');

function show_guidle_events_list($attrs) {
    if (is_list($attrs)) {
        return get_event_list(
            get_event_list_url($attrs),
            get_target_wp_page_url(($attrs))
        );
    }
    return get_event_details(
        get_event_details_base_url($attrs),
        $_GET['id']
    );
}

function get_event_list_as_json($eventListUrl) {
    $ch = curl_init();
    curl_setopt($ch, CURLOPT_URL, $eventListUrl);
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
    $data = curl_exec($ch);
    curl_close($ch);
    return json_decode($data, true);
}

function get_event_details_as_json($eventDetailsBaseUrl, $eventId) {
    $ch = curl_init();
    curl_setopt($ch, CURLOPT_URL, $eventDetailsBaseUrl . $eventId);
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
    $data = curl_exec($ch);
    curl_close($ch);
    return json_decode($data, true);
}

function get_attributes($attrs) {
    $defaultAttributes = array(
        'event-list-url' => '',
        'target-wp-page-url' => '',
        'event-details-base-url' => '',        
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

function get_target_wp_page_url($attrs) {
    return get_attributes($attrs)['target-wp-page-url'];
}

function get_event_list($eventListUrl, $targetWPPageUrl) {
    $eventListJson = get_event_list_as_json($eventListUrl);
    $smarty = new Smarty();
    $template = GUIDLE_EVENTS_PLUGIN_PATH . 'includes/templates/event-list.html';
    $finalWPPageUrl = $targetWPPageUrl;
    if (str_contains($targetWPPageUrl, "?")) {
        $finalWPPageUrl .= '&id=';
    } else {
        $finalWPPageUrl .= '?id=';
    }
    $smarty->assign('events', $eventListJson);
    $smarty->assign('finalWPPageUrl', $finalWPPageUrl);
    return $smarty->fetch($template);
}

function get_event_details($eventDetailsBaseUrl, $eventId) {
    $eventDetailsJson = get_event_details_as_json($eventDetailsBaseUrl, $eventId);
    $smarty = new Smarty();
    $template = GUIDLE_EVENTS_PLUGIN_PATH . 'includes/templates/event-details.html';
    $smarty->assign('event', $eventDetailsJson);
    return $smarty->fetch($template);
}

