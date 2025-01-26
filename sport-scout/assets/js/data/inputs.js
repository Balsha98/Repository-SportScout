// prettier-ignore
export const adminInputs = {
    add: {
        user: [
            "new_username", 
            "new_user_password", 
            "new_user_role_name", 
            "new_user_league_id", "new_user_team_id"
        ],
        sport: [
            "new_sport_name"
        ],
        league: [
            "new_league_name", 
            "new_league_sport_id"
        ],
        season: [
            "new_season_year", 
            "new_season_sport_id", 
            "new_season_league_id", 
            "new_season_desc"
        ],
        team: [
            "new_team_name",
            "new_team_sport_id",
            "new_team_league_id",
            "new_team_season_id",
            "new_team_max_players",
            "new_team_home_color",
            "new_team_away_color",
        ],
        position: [
            "new_position_name", 
            "new_position_sport_id"
        ],
    },
    alter: {
        user: [
            "user_id",
            "username",
            "user_role_name",
            "user_league_name",
            "user_league_id",
            "user_team_name",
            "user_team_id",
        ],
        sport: [
            "sport_name", 
            "sport_id"
        ],
        league: [
            "league_id", 
            "league_name", 
            "league_sport_name", 
            "league_sport_id"
        ],
        season: [
            "season_id",
            "season_year",
            "season_desc",
            "season_sport_name",
            "season_sport_id",
            "season_league_name",
            "season_league_id",
        ],
        team: [
            "team_sport_id",
            "team_name",
            "team_id",
            "team_sport_name",
            "team_league_id",
            "team_season_id",
            "team_max_players",
            "team_home_color",
            "team_away_color",
        ],
        position: [
            "position_id", 
            "position_name", 
            "position_sport_name", 
            "position_sport_id"
        ],
    }
};

// prettier-ignore
export const scheduleInputs = {
    add: {
        schedule: [
            "new_schedule_team_id",
            "new_schedule_sport_id",
            "new_schedule_league_id",
            "new_schedule_season_id",
            "new_schedule_home_team_id",
            "new_schedule_home_score",
            "new_schedule_away_team_id",
            "new_schedule_away_score",
            "new_schedule_date",
            "new_schedule_completion_status",
        ],
    },
    alter: {
        schedule: [
            "schedule_team_id",
            "schedule_season_id",
            "schedule_home_team_id",
            "schedule_home_score",
            "schedule_away_team_id",
            "schedule_away_score",
            "schedule_date",
            "schedule_completion_status",
        ],
        team: [
            "team_id", 
            "team_name", 
            "team_home_color", 
            "team_away_color"
        ],
    },
};

// prettier-ignore
export const teamInputs = {
    add: {
        player: [
            "new_player_sport_id",
            "new_player_league_name",
            "new_player_team_id",
            "new_player_first",
            "new_player_last",
            "new_player_dob",
            "new_player_position_id",
            "new_player_jersey_number",
        ],
        user: [
            "new_user_league_id",
            "new_user_team_id",
            "new_username",
            "new_user_password",
            "new_user_role_name",
            "new_user_league_name",
        ],
    },
    alter: {
        player: [
            "player_sport_id",
            "player_first",
            "player_last",
            "player_dob",
            "player_position_id",
            "player_jersey_number",
        ],
        user: [
            "staff_username", 
            "staff_role_name"
        ],
    },
};
