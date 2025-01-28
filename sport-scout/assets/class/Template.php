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

    public static function generateAdminPopups($db, $startIndex)
    {
        return AdminTemplate::generatePopups($db, $startIndex);
    }

    public static function generateAdminUsersDataContainer($db, $data)
    {
        return AdminTemplate::generateUsersDataContainer($db, $data);
    }

    public static function generateAdminSportsDataContainer($data)
    {
        return AdminTemplate::generateSportsDataContainer($data);
    }

    public static function generateAdminLeaguesDataContainer($db, $data, $roleID)
    {
        return AdminTemplate::generateLeaguesDataContainer($db, $data, $roleID);
    }

    public static function generateAdminSeasonsDataContainer($db, $data, $roleID)
    {
        return AdminTemplate::generateSeasonsDataContainer($db, $data, $roleID);
    }

    public static function generateAdminTeamsDataContainer($db, $data, $roleID)
    {
        return AdminTemplate::generateTeamsDataContainer($db, $data, $roleID);
    }

    public static function generateAdminPositionsDataContainer($db, $data, $roleID)
    {
        return AdminTemplate::generatePositionsDataContainer($db, $data, $roleID);
    }

    public static function generateTeamPopups($db, $teamData)
    {
        return TeamTemplate::generatePopups($db, $teamData);
    }

    public static function generateTeamPlayersData($data, $roleID, $leagueName)
    {
        return TeamTemplate::generatePlayersData($data, $roleID, $leagueName);
    }

    public static function generateTeamStaffData($data, $roleID, $leagueName)
    {
        return TeamTemplate::generateStaffData($data, $roleID, $leagueName);
    }

    public static function generateSchedulePopups($db, $teamData)
    {
        return ScheduleTemplate::generatePopups($db, $teamData);
    }

    public static function generateScheduleTeamData($data, $roleID)
    {
        return ScheduleTemplate::generateTeamData($data, $roleID);
    }

    public static function generateScheduleGame($db, $data, $roleID)
    {
        return ScheduleTemplate::generateGame($db, $data, $roleID);
    }

    public static function generateNoneSelectedDiv($data)
    {
        return ReusableTemplate::generateNoneSelectedDiv($data);
    }
}
