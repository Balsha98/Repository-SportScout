<?php

require_once '../template/ReusableTemplate.php';
require_once '../template/GeneralTemplate.php';
require_once '../template/AdminTemplate.php';
require_once '../template/TeamTemplate.php';
require_once '../template/ScheduleTemplate.php';

class Template
{
    static function generate_page_head($data)
    {
        return GeneralTemplate::generate_page_head($data);
    }

    static function generate_popup_overlay()
    {
        return GeneralTemplate::generate_popup_overlay();
    }

    static function generate_page_header($page, $role_id)
    {
        return GeneralTemplate::generate_page_header($page, $role_id);
    }

    static function generate_dashboard_links($role_id)
    {
        return GeneralTemplate::generate_dashboard_links($role_id);
    }

    static function generate_page_footer()
    {
        return GeneralTemplate::generate_page_footer();
    }

    static function generate_admin_links($start_index)
    {
        return AdminTemplate::generate_links($start_index);
    }

    static function generate_admin_popups($start_index)
    {
        return AdminTemplate::generate_popups($start_index);
    }

    static function generate_admin_users_data_container($data)
    {
        return AdminTemplate::generate_users_data_container($data);
    }

    static function generate_admin_sports_data_container($data)
    {
        return AdminTemplate::generate_sports_data_container($data);
    }

    static function generate_admin_leagues_data_container($data, $role_id)
    {
        return AdminTemplate::generate_leagues_data_container($data, $role_id);
    }

    static function generate_admin_seasons_data_container($data, $role_id)
    {
        return AdminTemplate::generate_seasons_data_container($data, $role_id);
    }

    static function generate_admin_positions_data_container($data, $role_id)
    {
        return AdminTemplate::generate_positions_data_container($data, $role_id);
    }

    static function generate_admin_teams_data_container($data, $role_id)
    {
        return AdminTemplate::generate_teams_data_container($data, $role_id);
    }

    static function generate_team_popups($team_id)
    {
        return TeamTemplate::generate_popups($team_id);
    }

    static function generate_team_players_data($data, $role_id, $league_name)
    {
        return TeamTemplate::generate_players_data($data, $role_id, $league_name);
    }

    static function generate_team_staff_data($data, $role_id, $league_name)
    {
        return TeamTemplate::generate_staff_data($data, $role_id, $league_name);
    }

    static function generate_schedule_popups($team_id)
    {
        return ScheduleTemplate::generate_popups($team_id);
    }

    static function generate_schedule_team_data($data, $role_id)
    {
        return ScheduleTemplate::generate_team_data($data, $role_id);
    }

    static function generate_schedule_game($data, $role_id)
    {
        return ScheduleTemplate::generate_game($data, $role_id);
    }

    static function generate_none_selected_div($data)
    {
        return ReusableTemplate::generate_none_selected_div($data);
    }
}
