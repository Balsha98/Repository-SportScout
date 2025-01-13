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

    public static function generatePageHeader($page, $roleID)
    {
        return GeneralTemplate::generatePageHeader($page, $roleID);
    }

    public static function generateDashboardLinks($roleID)
    {
        return GeneralTemplate::generateDashboardLinks($roleID);
    }

    public static function generatePageFooter()
    {
        return GeneralTemplate::generatePageFooter();
    }

    public static function generateAdminLinks($startIndex)
    {
        return AdminTemplate::generateLinks($startIndex);
    }

    public static function generateAdminPopups($startIndex)
    {
        return AdminTemplate::generatePopups($startIndex);
    }

    public static function generateAdminUsersDataContainer($data)
    {
        return AdminTemplate::generateUsersDataContainer($data);
    }

    public static function generateAdminSportsDataContainer($data)
    {
        return AdminTemplate::generateSportsDataContainer($data);
    }

    public static function generateAdminLeaguesDataContainer($data, $roleID)
    {
        return AdminTemplate::generateLeaguesDataContainer($data, $roleID);
    }

    public static function generateAdminSeasonsDataContainer($data, $roleID)
    {
        return AdminTemplate::generateSeasonsDataContainer($data, $roleID);
    }

    public static function generateAdminPositionsDataContainer($data, $roleID)
    {
        return AdminTemplate::generatePositionsDataContainer($data, $roleID);
    }

    public static function generateAdminTeamsDataContainer($data, $roleID)
    {
        return AdminTemplate::generateTeamsDataContainer($data, $roleID);
    }

    public static function generateTeamPopups($teamData)
    {
        return TeamTemplate::generatePopups($teamData);
    }

    public static function generateTeamPlayersData($data, $roleID, $leagueName)
    {
        return TeamTemplate::generatePlayersData($data, $roleID, $leagueName);
    }

    public static function generateTeamStaffData($data, $roleID, $leagueName)
    {
        return TeamTemplate::generateStaffData($data, $roleID, $leagueName);
    }

    public static function generateSchedulePopups($teamData)
    {
        return ScheduleTemplate::generatePopups($teamData);
    }

    public static function generateScheduleTeamData($data, $roleID)
    {
        return ScheduleTemplate::generateTeamData($data, $roleID);
    }

    public static function generateScheduleGame($data, $roleID)
    {
        return ScheduleTemplate::generateGame($data, $roleID);
    }

    public static function generateNoneSelectedDiv($data)
    {
        return ReusableTemplate::generateNoneSelectedDiv($data);
    }
}
