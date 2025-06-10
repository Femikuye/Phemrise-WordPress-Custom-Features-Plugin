<?php

    use PhemriseIct\PhemriseWpCf\Features\Idx\Idx;
    $idx = new Idx();
    $url = 'https://teetimesolutions.georgiamls.com/real-estate/search-action.cfm?city=Augusta';
    $opt_name = $idx->plugin_opt_name;
    $page_content = $idx->fetchPageIdxPage($url);

    print_r($idx->scrapeIdxPageData($page_content));

?>

<h1>IDX Dashboard: <?php echo $opt_name; ?></h1>