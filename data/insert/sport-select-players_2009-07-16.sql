
SELECT DISTINCT `id`, `name`, `surname`, `birthyear`, `number`, `position`, `photo`, 

(SELECT COUNT(`pid`) AS `matches` FROM `w_sport_stats` WHERE `pid` = `player`.`id`) AS `total_matches`, 
(SELECT SUM(`goals`) AS `matches` FROM `w_sport_stats` WHERE `pid` = `player`.`id`) AS `total_goals`, 
(SELECT SUM(`assists`) AS `matches` FROM `w_sport_stats` WHERE `pid` = `player`.`id`) AS `total_assists`, 
(SELECT SUM(`penalty`) AS `matches` FROM `w_sport_stats` WHERE `pid` = `player`.`id`) AS `total_penalty`, 
(SELECT SUM(`shoots`) AS `matches` FROM `w_sport_stats` WHERE `pid` = `player`.`id`) AS `total_shoots`, 
(SELECT (SUM(`shoots`) / (SUM(`shoots`) + SUM(`goals`)) * 100) AS `matches` FROM `w_sport_stats` WHERE `pid` = `player`.`id`) AS `total_percentage`,
(SELECT (SUM(`goals`) / COUNT(`pid`)) AS `matches` FROM `w_sport_stats` WHERE `pid` = `player`.`id`) AS `total_average`, 
(SELECT (SUM(`goals`) + SUM(`assists`)) AS `matches` FROM `w_sport_stats` WHERE `pid` = `player`.`id`) AS `total_points`

(SELECT COUNT(`pid`) AS `matches` FROM `w_sport_stats` WHERE `w_sport_stats`.`season` = '.$seasonId.' AND `pid` = `player`.`id`) AS `season_matches`, 
(SELECT SUM(`goals`) AS `matches` FROM `w_sport_stats` WHERE `w_sport_stats`.`season` = '.$seasonId.' AND `pid` = `player`.`id`) AS `season_goals`, 
(SELECT SUM(`assists`) AS `matches` FROM `w_sport_stats` WHERE `w_sport_stats`.`season` = '.$seasonId.' AND `pid` = `player`.`id`) AS `season_assists`, 
(SELECT SUM(`penalty`) AS `matches` FROM `w_sport_stats` WHERE `w_sport_stats`.`season` = '.$seasonId.' AND `pid` = `player`.`id`) AS `season_penalty`, 
(SELECT SUM(`shoots`) AS `matches` FROM `w_sport_stats` WHERE `w_sport_stats`.`season` = '.$seasonId.' AND `pid` = `player`.`id`) AS `season_shoots`, 
(SELECT (SUM(`shoots`) / (SUM(`shoots`) + SUM(`goals`)) * 100) AS `matches` FROM `w_sport_stats` WHERE `w_sport_stats`.`season` = '.$seasonId.' AND `pid` = `player`.`id`) AS `season_percentage`,
(SELECT (SUM(`goals`) / COUNT(`pid`)) AS `matches` FROM `w_sport_stats` WHERE `w_sport_stats`.`season` = '.$seasonId.' AND `pid` = `player`.`id`) AS `season_average`, 
(SELECT (SUM(`goals`) + SUM(`assists`)) AS `matches` FROM `w_sport_stats` WHERE `w_sport_stats`.`season` = '.$seasonId.' AND `pid` = `player`.`id`) AS `season_points`

(SELECT COUNT(`pid`) AS `matches` FROM `w_sport_stats` WHERE `w_sport_stats`.`mid` = '.$fromMatchId.' AND `pid` = `player`.`id`) AS `match_matches`, 
(SELECT SUM(`goals`) AS `matches` FROM `w_sport_stats` WHERE `w_sport_stats`.`mid` = '.$fromMatchId.' AND `pid` = `player`.`id`) AS `match_goals`, 
(SELECT SUM(`assists`) AS `matches` FROM `w_sport_stats` WHERE `w_sport_stats`.`mid` = '.$fromMatchId.' AND `pid` = `player`.`id`) AS `match_assists`, 
(SELECT SUM(`penalty`) AS `matches` FROM `w_sport_stats` WHERE `w_sport_stats`.`mid` = '.$fromMatchId.' AND `pid` = `player`.`id`) AS `match_penalty`, 
(SELECT SUM(`shoots`) AS `matches` FROM `w_sport_stats` WHERE `w_sport_stats`.`mid` = '.$fromMatchId.' AND `pid` = `player`.`id`) AS `match_shoots`, 
(SELECT (SUM(`shoots`) / (SUM(`shoots`) + SUM(`goals`)) * 100) AS `matches` FROM `w_sport_stats` WHERE `w_sport_stats`.`mid` = '.$fromMatchId.' AND `pid` = `player`.`id`) AS `match_percentage`,
(SELECT (SUM(`goals`) / COUNT(`pid`)) AS `matches` FROM `w_sport_stats` WHERE `w_sport_stats`.`mid` = '.$fromMatchId.' AND `pid` = `player`.`id`) AS `match_average`, 
(SELECT (SUM(`goals`) + SUM(`assists`)) AS `matches` FROM `w_sport_stats` WHERE `w_sport_stats`.`mid` = '.$fromMatchId.' AND `pid` = `player`.`id`) AS `match_points`

 FROM `w_sport_player` AS `player` WHERE `id` = '.$playerId.' AND `season` = '.$seasonId.';
 
 -- -------------------------------------------------------------------------------------------
 
(SELECT COUNT(`pid`) AS `matches` FROM `w_sport_stats` WHERE `pid` = `player`.`id`) AS `total_matches`, (SELECT SUM(`goals`) AS `matches` FROM `w_sport_stats` WHERE `pid` = `player`.`id`) AS `total_goals`, (SELECT SUM(`assists`) AS `matches` FROM `w_sport_stats` WHERE `pid` = `player`.`id`) AS `total_assists`, (SELECT SUM(`penalty`) AS `matches` FROM `w_sport_stats` WHERE `pid` = `player`.`id`) AS `total_penalty`, (SELECT SUM(`shoots`) AS `matches` FROM `w_sport_stats` WHERE `pid` = `player`.`id`) AS `total_shoots`, (SELECT (SUM(`shoots`) / (SUM(`shoots`) + SUM(`goals`)) * 100) AS `matches` FROM `w_sport_stats` WHERE `pid` = `player`.`id`) AS `total_percentage`,(SELECT (SUM(`goals`) / COUNT(`pid`)) AS `matches` FROM `w_sport_stats` WHERE `pid` = `player`.`id`) AS `total_average`, (SELECT (SUM(`goals`) + SUM(`assists`)) AS `matches` FROM `w_sport_stats` WHERE `pid` = `player`.`id`) AS `total_points`
 
 -- -------------------------------------------------------------------------------------------
 
(SELECT COUNT(`pid`) AS `matches` FROM `w_sport_stats` WHERE `w_sport_stats`.`season` = '.$seasonId.' AND `pid` = `player`.`id`) AS `season_matches`, (SELECT SUM(`goals`) AS `matches` FROM `w_sport_stats` WHERE `w_sport_stats`.`season` = '.$seasonId.' AND `pid` = `player`.`id`) AS `season_goals`, (SELECT SUM(`assists`) AS `matches` FROM `w_sport_stats` WHERE `w_sport_stats`.`season` = '.$seasonId.' AND `pid` = `player`.`id`) AS `season_assists`, (SELECT SUM(`penalty`) AS `matches` FROM `w_sport_stats` WHERE `w_sport_stats`.`season` = '.$seasonId.' AND `pid` = `player`.`id`) AS `season_penalty`, (SELECT SUM(`shoots`) AS `matches` FROM `w_sport_stats` WHERE `w_sport_stats`.`season` = '.$seasonId.' AND `pid` = `player`.`id`) AS `season_shoots`, (SELECT (SUM(`shoots`) / (SUM(`shoots`) + SUM(`goals`)) * 100) AS `matches` FROM `w_sport_stats` WHERE `w_sport_stats`.`season` = '.$seasonId.' AND `pid` = `player`.`id`) AS `season_percentage`,(SELECT (SUM(`goals`) / COUNT(`pid`)) AS `matches` FROM `w_sport_stats` WHERE `w_sport_stats`.`season` = '.$seasonId.' AND `pid` = `player`.`id`) AS `season_average`, (SELECT (SUM(`goals`) + SUM(`assists`)) AS `matches` FROM `w_sport_stats` WHERE `w_sport_stats`.`season` = '.$seasonId.' AND `pid` = `player`.`id`) AS `season_points`

 -- -------------------------------------------------------------------------------------------
 
(SELECT COUNT(`pid`) AS `matches` FROM `w_sport_stats` WHERE `w_sport_stats`.`mid` = '.$fromMatchId.' AND `pid` = `player`.`id`) AS `match_matches`, (SELECT SUM(`goals`) AS `matches` FROM `w_sport_stats` WHERE `w_sport_stats`.`mid` = '.$fromMatchId.' AND `pid` = `player`.`id`) AS `match_goals`, (SELECT SUM(`assists`) AS `matches` FROM `w_sport_stats` WHERE `w_sport_stats`.`mid` = '.$fromMatchId.' AND `pid` = `player`.`id`) AS `match_assists`, (SELECT SUM(`penalty`) AS `matches` FROM `w_sport_stats` WHERE `w_sport_stats`.`mid` = '.$fromMatchId.' AND `pid` = `player`.`id`) AS `match_penalty`, (SELECT SUM(`shoots`) AS `matches` FROM `w_sport_stats` WHERE `w_sport_stats`.`mid` = '.$fromMatchId.' AND `pid` = `player`.`id`) AS `match_shoots`, (SELECT (SUM(`shoots`) / (SUM(`shoots`) + SUM(`goals`)) * 100) AS `matches` FROM `w_sport_stats` WHERE `w_sport_stats`.`mid` = '.$fromMatchId.' AND `pid` = `player`.`id`) AS `match_percentage`,(SELECT (SUM(`goals`) / COUNT(`pid`)) AS `matches` FROM `w_sport_stats` WHERE `w_sport_stats`.`mid` = '.$fromMatchId.' AND `pid` = `player`.`id`) AS `match_average`, (SELECT (SUM(`goals`) + SUM(`assists`)) AS `matches` FROM `w_sport_stats` WHERE `w_sport_stats`.`mid` = '.$fromMatchId.' AND `pid` = `player`.`id`) AS `match_points`