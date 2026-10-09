<?php declare(strict_types=1);

namespace System;

use System\RouteNotFoundException;

/**
 * Class Router
 *
 * This router uses a fluent, method-based API for route definition.
 * It is optimized for PHP 8.3 with readonly properties and strict typing.
 */
final class Router
{
    /** @var array<Route> */
    private array $routes = [];

    /**
     * Define a GET route.
     * @param string $uri The URI pattern (e.g., '/', '/users/{id}')
     * @param string $controllerAction Controller and action in 'Controller@action' format
     */
    public function get(string $uri, string $controllerAction): void
    {
        $this->addRoute('GET', $uri, $controllerAction);
    }

    /**
     * Define a POST route.
     * @param string $uri The URI pattern
     * @param string $controllerAction Controller and action in 'Controller@action' format
     */
    public function post(string $uri, string $controllerAction): void
    {
        $this->addRoute('POST', $uri, $controllerAction);
    }

    /**
     * Define a PUT route.
     * @param string $uri The URI pattern
     * @param string $controllerAction Controller and action in 'Controller@action' format
     */
    public function put(string $uri, string $controllerAction): void
    {
        $this->addRoute('PUT', $uri, $controllerAction);
    }

    /**
     * Define a DELETE route.
     * @param string $uri The URI pattern
     * @param string $controllerAction Controller and action in 'Controller@action' format
     */
    public function delete(string $uri, string $controllerAction): void
    {
        $this->addRoute('DELETE', $uri, $controllerAction);
    }
    
    /**
     * Define a route for both GET and POST requests.
     * @param string $uri The URI pattern
     * @param string $controllerAction Controller and action in 'Controller@action' format
     */
    public function any(string $uri, string $controllerAction): void
    {
        $this->addRoute('ANY', $uri, $controllerAction);
    }
    
    /**
     * The core method to add a route to the router.
     *
     * @param string $method The HTTP method (e.g., 'GET', 'POST').
     * @param string $uri The URI pattern.
     * @param string $controllerAction Controller and action in 'Controller@action' format.
     */
    private function addRoute(string $method, string $uri, string $controllerAction): void
    {
        $uri = trim($uri, '/');
        
        $pattern = $this->buildPattern($uri);
        [$controller, $action] = $this->parseControllerAction($controllerAction);

        $this->routes[] = new Route(
            method: strtoupper($method),
            uri: $uri,
            controller: $controller,
            action: $action,
            pattern: $pattern
        );
    }

    /**
     * Route the request to the appropriate controller and action.
     *
     * @param string $uri The requested URI.
     * @param string $method The request method (e.g., 'GET').
     * @return array An array containing controller, action, and parameters.
     * @throws RouteNotFoundException If no matching route is found.
     */
    public function route(string $uri, string $method): array
    {
        $uri = trim($uri, '/');
        $method = strtoupper($method);

        foreach ($this->routes as $route) {
            // Check for a match on the method or if the method is 'ANY'
            if (($route->method === $method || $route->method === 'ANY') && preg_match($route->pattern, $uri, $matches)) {
                array_shift($matches); // Remove the full match

                return [
                    'controller' => $route->controller,
                    'action' => $route->action,
                    'params' => $matches,
                ];
            }
        }

        throw new RouteNotFoundException();
    }

    /**
     * Build the regex pattern for the URI, supporting dynamic parameters.
     *
     * @param string $uri The original URI string.
     * @return string The regex pattern.
     */
    private function buildPattern(string $uri): string
    {
        $uri = $uri ?: '';
        
        // Escape all characters that might have special regex meaning
        $pattern = preg_quote($uri, '#');

        // Replace dynamic parameters like {id} with a capturing group
        $pattern = preg_replace('/\\\{(\w+)\\\}/', '([^/]+)', $pattern);

        return '#^' . $pattern . '$#i';
    }

    /**
     * Parses the 'Controller@action' string.
     *
     * @param string $controllerAction
     * @return array<string> An array containing the controller and action name.
     */
    private function parseControllerAction(string $controllerAction): array
    {
        $parts = explode('@', $controllerAction);
        if (count($parts) !== 2) {
            throw new \InvalidArgumentException("Invalid controller action format. Must be 'Controller@action'.");
        }
        return $parts;
    }
}
