<?php

require_once 'assets/template/ReusableTemplate.php';
require_once 'assets/template/GeneralTemplate.php';
require_once 'assets/template/AdminTemplate.php';
require_once 'assets/template/TeamTemplate.php';
require_once 'assets/template/ScheduleTemplate.php';

class Template
{
    public static function generatePageHead($data)
    {
        return GeneralTemplate::generatePageHead($data);
    }

    public static function generatePopupOverlay()
    {
        return GeneralTemplate::generatePopupOverlay();
    }

    public static function generatePageHeader($page, $role_id)
    {
        return GeneralTemplate::generatePageHeader($page, $role_id);
    }

    public static function generateDashboardLinks($role_id)
    {
        return GeneralTemplate::generateDashboardLinks($role_id);
    }

    public static function generatePageFooter()
    {
        return GeneralTemplate::generatePageFooter();
    }

    public static function generateAdminLinks($start_index)
    {
        return AdminTemplate::generateLinks($start_index);
    }

    public static function generateAdminPopups($start_index)
    {
        return AdminTemplate::generatePopups($start_index);
    }

    public static function generateAdminUsersDataContainer($data)
    {
        return AdminTemplate::generateUsersDataContainer($data);
    }

    public static function generateAdminSportsDataContainer($data)
    {
        return AdminTemplate::generateSportsDataContainer($data);
    }

    public static function generateAdminLeaguesDataContainer($data, $role_id)
    {
        return AdminTemplate::generateLeaguesDataContainer($data, $role_id);
    }

    public static function generateAdminSeasonsDataContainer($data, $role_id)
    {
        return AdminTemplate::generateSeasonsDataContainer($data, $role_id);
    }

    public static function generateAdminPositionsDataContainer($data, $role_id)
    {
        return AdminTemplate::generatePositionsDataContainer($data, $role_id);
    }

    public static function generateAdminTeamsDataContainer($data, $role_id)
    {
        return AdminTemplate::generateTeamsDataContainer($data, $role_id);
    }

    public static function generateTeamPopups($team_id)
    {
        return TeamTemplate::generatePopups($team_id);
    }

    public static function generateTeamPlayersData($data, $role_id, $league_name)
    {
        return TeamTemplate::generatePlayersData($data, $role_id, $league_name);
    }

    public static function generateTeamStaffData($data, $role_id, $league_name)
    {
        return TeamTemplate::generateStaffData($data, $role_id, $league_name);
    }

    public static function generateSchedulePopups($team_id)
    {
        return ScheduleTemplate::generatePopups($team_id);
    }

    public static function generateScheduleTeamData($data, $role_id)
    {
        return ScheduleTemplate::generateTeamData($data, $role_id);
    }

    public static function generateScheduleGame($data, $role_id)
    {
        return ScheduleTemplate::generateGame($data, $role_id);
    }

    public static function generateNoneSelectedDiv($data)
    {
        return ReusableTemplate::generateNoneSelectedDiv($data);
    }
}
