<?php
/**
 * @OA\Info(
 * title="Direct Finds API",
 * description="Direct Finds is a front-end single-page application (SPA) that provides the user interface for a modern peer-to-peer marketplace. It allows users to browse products, view seller shops, and, for registered users, list their own items for sale. This project was built from the ground up using fundamental web technologies to demonstrate solid programming principles without reliance on heavy frameworks. The single-page application experience is powered by the lightweight spapp jQuery library for smooth, client-side routing.",
 * version="1.0",
 * @OA\Contact(
 * email="anel.brcaninovic@stu.ibu.edu.ba",
 * name="Anel Brčaninović"
 * )
 * )
 */
/**
 * @OA\Server(
 * url= "http://localhost/webShop/back-end",
 * description="API server"
 * )
 * 
 * @OA\Server(
 * url= "http://deployment-server-address/",
 * description="API server"
 * )
 * 
 * 
 */
/**
 * @OA\SecurityScheme(
 * securityScheme="ApiKey",
 * type="apiKey",
 * in="header",
 * name="Authentication"
 * )
 */