<?php
namespace AppBundle\Common;

class Constants
{
	const PAGE_SIZE_VAL			= 10;
	const WEBROOT_PHYSICAL		= "/path/to/physical";
	const WEBROOT				= "/";
	const ASSET_ROOT			= "/web/";
	
	const ACTION				= "action";
	const WRITE					= "write";
	const EDIT					= "edit";
	
	const FILES					= "files";
	const THUMBNAIL_IMAGE 		= "thumbnailImage";
	const MAIN_IMAGE 			= "mainImage";
	const IMAGE1 				= "image1";
	const IMAGE2 				= "image2";
	const IMAGE3 				= "image3";
	const BROCHURE 				= "brochure";
	const ATTACHMENT			= "attachment";
	
	const PRODUCT				= "product";
	const SERVICE				= "service";
	const NEWS 					= "news";
	const EVENT 				= "event";
	const CONTACT				= "contact";
	const PROJECT				= "project";
	const CATALOG				= "catalog";
	const USER					= "user";
	const CAREER				= "career";
	const TERM					= "term";
	const TAG					= "tag";
	const CATEGORY				= "category";
	const DASHBOARD				= "dashboard";
	
	const UPLOAD_PRODUCT_PATH 	= "/upload/product";
	const UPLOAD_SERVICE_PATH 	= "/upload/service";
	const UPLOAD_NEWS_PATH 		= "/upload/news";
	const UPLOAD_EVENT_PATH 	= "/upload/event";
	const UPLOAD_CONTACT_PATH 	= "/upload/contact";
	const UPLOAD_PROJECT_PATH 	= "/upload/project";
	const UPLOAD_CATALOG_PATH 	= "/upload/catalog";
	const UPLOAD_USER_PATH 		= "/upload/user";
	const UPLOAD_TERM_PATH		= "/upload/term";
	const UPLOAD_CAREER_PATH	= "/upload/career";
	
	/* To be used in page & search navigation  (URL Parameter) */
	const PARAMETER_PAGE		= "p";
	const PARAMETER_CONDITION	= "c";
	const PARAMETER_KEYWORD		= "k";
	
	/* Used as keys in associative array (data) passed to view layer */
	const CONTENT_PAGE_TITLE	= "pageTitle";
	const CONTENT_MENU			= "menu";
	const CONTENT_PARAM			= "param";
	const CONTENT_PAGING		= "paging";
	
	const CURRENT				= "current";
	const PARAMETER				= "parameter";
	const PAGE_SIZE				= "pageSize";
	const TOTAL_ROWS			= "totalRows";
	
	const DATA					= "data";
	const ERRORS				= "errors";
	
}
