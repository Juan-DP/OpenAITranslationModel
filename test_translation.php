<?php

require_once(__DIR__ ."/../libs/OpenAI/OpenAIConnector.php");

$connector = new OpenAIConnector();

//Test model with single input
var_dump(OpenAIClient::translateFromText($connector, "Compre agora os pacotes de hospitalidade da 'CompanyTeam', agora com F1 2024 disponível."));
//End test model

//Test model with several inputs
$model='ft:gpt-3.5-turbo-1106:personal::8srXZ2H4';
$input = [
    "Please, transalate to Chinese on the context of a hospitality ticketing website. 'Experience the history of the UEFA EURO 2024™ and select from a range of final series packages, including Semi-Final matches in Munich and Dortmund and the big Finale in Berlin!
    The best memories are made on days like this: drive into your parking lot, walk directly to the Hospitality entrance, be greeted with a glass of Champagne from our eager-to-please hosting team, savour the gourmet catering and soak up the wonderful live music and entertainment. And most importantly, enjoy the best view of the match.
    Be among the first to claim your seat for UEFA EURO 2024™ with official Hospitality products.'",
    "Please, transalate to Chinese on the context of a hospitality ticketing website. 'Ralf Schmitz already wowed his Cologne audience in the LANXESS arena last year with his Schmitzefrei program. He will seek to repeat this in 2024 when he will again be a guest in Germany's largest multifunctional arena on March 2nd, 2024.
    Being on stage is like a vacation for me! Your laughter is my five star dessert your applause is my infinity pool! Unfortunately, real vacation is mostly hard work!”
    Guarantee your VIP ticket to see Ralf Schmitz live at the Lanxess Arena in Cologne with hospitality packages available on DAIMANI.'",
    "Please, transalate to Chinese on the context of a hospitality ticketing website. 'Berlin, Berlin, we're going to Berlin!  The DFB Cup Final in Berlin is the fascinating highlight of a knockout competition which starts with 64 teams from the first, second, third league as well as amateur teams. With the tickets for the final sold, it's all about Berlin Olympic Stadium for the last two teams. The German capital is always the place to be every year on a special Saturday in the end of May! Be our guest and enjoy the number one sport event in Germany!'",
];

foreach($input as $inputContext) {
    $messages = [
        [
            "role" => "user",
            "content" => $inputContext
        ]
    ];
    
    $gptOutput = OpenAIClient::chat($messages, 10000, $model);
    
    $aiSuggestedTranslation = $gptOutput['choices'][0]['message']['content'];
    echo $aiSuggestedTranslation . '<br>';

}
