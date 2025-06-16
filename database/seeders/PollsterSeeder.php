<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class PollsterSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $pollsters = [
            ['name' => 'Gallup', 'rank' => 'A+', 'description' => 'Leading global analytics and advice firm.', 'website' => 'https://www.gallup.com'],
            ['name' => 'Pew Research', 'rank' => 'A', 'description' => 'Nonpartisan fact tank that informs the public.', 'website' => 'https://www.pewresearch.org'],
            ['name' => 'YouGov', 'rank' => 'B', 'description' => 'International research data and analytics group.', 'website' => 'https://today.yougov.com'],
            ['name' => 'Ipsos', 'rank' => 'A+', 'description' => 'Global market research and consulting firm.', 'website' => 'https://www.ipsos.com'],
            ['name' => 'SurveyUSA', 'rank' => 'B', 'description' => 'Known for quick public opinion polling.', 'website' => 'https://www.surveyusa.com'],
            ['name' => 'Rasmussen Reports', 'rank' => 'C', 'description' => 'Specializes in political and economic polling.', 'website' => 'https://www.rasmussenreports.com'],
            ['name' => 'Morning Consult', 'rank' => 'A', 'description' => 'Technology-driven polling company.', 'website' => 'https://morningconsult.com'],
            ['name' => 'Quinnipiac University', 'rank' => 'A', 'description' => 'University-based polling institute.', 'website' => 'https://poll.qu.edu'],
            ['name' => 'Marist Poll', 'rank' => 'A', 'description' => 'Public opinion polls from Marist College.', 'website' => 'https://maristpoll.marist.edu'],
            ['name' => 'Monmouth University Polling Institute', 'rank' => 'A', 'description' => 'Independent polling by Monmouth University.', 'website' => 'https://www.monmouth.edu/polling-institute'],

            ['name' => 'Data for Progress', 'rank' => 'B', 'description' => 'Progressive think tank and polling firm.', 'website' => 'https://www.dataforprogress.org'],
            ['name' => 'Emerson College', 'rank' => 'B', 'description' => 'Polling unit from Emerson College.', 'website' => 'https://emersonpolling.com'],
            ['name' => 'Change Research', 'rank' => 'C', 'description' => 'Online polling for progressive campaigns.', 'website' => 'https://changeresearch.com'],
            ['name' => 'Trafalgar Group', 'rank' => 'C', 'description' => 'Polling firm with controversial methods.', 'website' => 'https://www.thetrafalgargroup.org'],
            ['name' => 'Fox News Poll', 'rank' => 'B', 'description' => 'Polls conducted by Fox News network.', 'website' => 'https://www.foxnews.com'],
            ['name' => 'NBC News / Wall Street Journal', 'rank' => 'A', 'description' => 'Joint political polling effort.', 'website' => 'https://www.nbcnews.com'],
            ['name' => 'CNN Polling', 'rank' => 'B', 'description' => 'Polling and surveys by CNN.', 'website' => 'https://www.cnn.com'],
            ['name' => 'ABC News / Washington Post', 'rank' => 'A+', 'description' => 'High-quality political polling.', 'website' => 'https://abcnews.go.com'],
            ['name' => 'CBS News / YouGov', 'rank' => 'B', 'description' => 'Collaborative political polling.', 'website' => 'https://www.cbsnews.com'],
            ['name' => 'Zogby Analytics', 'rank' => 'C', 'description' => 'Polling firm for public opinion research.', 'website' => 'https://www.zogbyanalytics.com'],

            ['name' => 'Franklin & Marshall', 'rank' => 'B', 'description' => 'College-based public opinion polling.', 'website' => 'https://www.fandm.edu'],
            ['name' => 'Langer Research', 'rank' => 'A+', 'description' => 'Social science survey design and analysis.', 'website' => 'https://www.langerresearch.com'],
            ['name' => 'NORC at UChicago', 'rank' => 'A', 'description' => 'Research at the University of Chicago.', 'website' => 'https://www.norc.org'],
            ['name' => 'Hart Research', 'rank' => 'B', 'description' => 'Public opinion research firm.', 'website' => 'https://hartresearch.com'],
            ['name' => 'Public Policy Polling (PPP)', 'rank' => 'C', 'description' => 'Democratic-leaning polling firm.', 'website' => 'https://www.publicpolicypolling.com'],
            ['name' => 'Lucid', 'rank' => 'B', 'description' => 'Online sample marketplace.', 'website' => 'https://luc.id'],
            ['name' => 'AtlasIntel', 'rank' => 'B', 'description' => 'Pollster with a focus on Latin America.', 'website' => 'https://www.atlasintel.org'],
            ['name' => 'Redfield & Wilton Strategies', 'rank' => 'B', 'description' => 'London-based polling company.', 'website' => 'https://redfieldandwiltonstrategies.com'],
            ['name' => 'Global Strategy Group', 'rank' => 'C', 'description' => 'Democratic strategic research firm.', 'website' => 'https://www.globalstrategygroup.com'],
            ['name' => 'Beacon Research', 'rank' => 'A', 'description' => 'Political research and polling firm.', 'website' => 'https://www.beaconresearch.com'],
            ['name' => 'Greenberg Quinlan Rosner', 'rank' => 'B', 'description' => 'Strategic research for progressive campaigns.', 'website' => 'https://www.gqrr.com'],
        ];

        DB::table('pollsters')->insert($pollsters);
    }
}
