<?php
require 'data.php';


/* -------------------------------------------------------------------------- */
/*                                FUNCTION                                	  */
/* -------------------------------------------------------------------------- */

/**
 *
 * Find all possible paths on the array flight from departure to arrival 
 * with a stepover of 3
 *
 * @param    array $flights
 * @param    int $departure
 * @param    int $arrival
 * @param    int $stepover
 * 
 * @return   array  $stepover
 *
 */
function findPaths(array $flights, $departure, $arrival, $stepover=0)
{
    $start_routes = $flights;

    if ($stepover>=3)
    {
       return false;
    }
    $stepover++;

    // Search if departure is present
    // and remove it if found in the array
    foreach ($start_routes as $key => $path) 
	{
        if ($path['code_departure'] != $departure) 
		{
            unset($start_routes[$key]);
        } 
		else 
		{
            unset($flights[$key]);
        }
    }

    // No departure found in the array
    if (count($start_routes) == 0) 
	{
        return false;
    }

    // Try to work out if we can find the route 
    $routes = [];
    foreach($start_routes as $path) 
	{
        if ($path['code_arrival'] == $arrival) 
		{
            $routes[]= $path['code_departure']."#".$path['code_arrival']."#".intval($path['price']);
            return $routes;
        }

        if (!$found = findPaths($flights, $path['code_arrival'], $arrival, $stepover)) 
		{
            continue;
        }
        
        $routes[$path['code_departure']."#".$path['code_arrival']."#".intval($path['price'])] = $found;

    }
    return $routes;

}

/**
 *
 * Next two functions below are used to formatting the array of routes
 * in order to working in a better way with it
 *
 */
function flatArray($paths, $pre = '', $lines = []) 
{
    foreach($paths as $key => $path) 
	{
        if($key=="0")
		{
            $key="";
        }

        if (is_array($path)) 
		{
            $lines = flatArray($path, $pre.$key."@", $lines);
        } 
		else 
		{
            $lines[] = $pre.$key.$path;
        }
    }

    return $lines;
}

function array_formatting($flats)
{
	$sharp_separated = [];
    $final_route = [];

	foreach ($flats as $flat)
	{
		$sharp_separated[] = explode("@", $flat);
	}

    $i = 0;
    foreach ($sharp_separated as $paths)
    {
        $route = "route".$i;
        foreach ($paths as $path)
        {
            $final_route[$route][] = explode("#", $path);
        }
        $i++;
    }
	
	return $final_route;
}

/**
 *
 * Return the same structure of routes array 
 * but with the total cost per travel
 *
 * @param   array $final_path
 * 
 * @return  array $cost_routes
 *
 */
function get_costs($final_path)
{
    $cost_routes = [];
    foreach ($final_path as $key => $value)
    {
        $total = 0;
        foreach ($value as $cost)
        {
            $total += $cost[2];
        }

        $cost_routes[$key] = $total;
    }

    return $cost_routes;
}

/**
 *
 * Return the routes array adding some fields
 * matching the name of airports/city
 *
 * @param   array $airports
 * @param   array $route
 * 
 * @return  array  $cost_routes
 *
 */
function get_coordinate($airports, $route)
{
    $route_coord = $route;
    foreach ($route as $key => $value)
    {
        $route_coord[$key]['from'] = get_info($value[0], $airports, 'lat').",".get_info($value[0], $airports, 'lng');
        $route_coord[$key]['to'] = get_info($value[1], $airports, 'lat').",".get_info($value[1], $airports, 'lng');
        $route_coord[$key]['from_city'] = get_info($value[0], $airports, 'name');
        $route_coord[$key]['to_city'] = get_info($value[1], $airports, 'name');
    }

    return $route_coord;
}

/**
 *
 * Search and return the informations
 * from the $airports array
 * 
 */
function get_info($id, $array, $search) 
{
    foreach ($array as $key => $val) 
    {
        if ($val['id'] == $id)
        {
            return $val[$search];
        }
    }
    return null;
 }

/**
 *
 * This functon is called form get_route.php
 * and return an array with the best/chip route
 *
 * @param   array $airports
 * @param   array $flights
 * @param   int $departure
 * @param   int $arrival
 * 
 * @return  array  $final_route
 *
 */
function main($airports, $flights, $departure, $arrival)
{
    // Get all possible routes 
    $paths = findPaths($flights, $departure, $arrival);

    if ( !$paths )
    {
        return false;
    }

    // Formatting the array of routes
    $flats = flatArray($paths);
    $paths = array_formatting($flats);

    // Get the cost per all the routes
    $costs = get_costs($paths);

    // Find the chippest route
    $min_route = array_keys($costs, min($costs));
    $best_route = $paths[$min_route[0]];

    // Adding some fields to the best route's array
    // used on the front-end
    $final_route = get_coordinate($airports, $best_route);

    return $final_route;
}


