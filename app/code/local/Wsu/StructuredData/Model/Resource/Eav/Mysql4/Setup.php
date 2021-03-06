<?php
class Wsu_StructuredData_Model_Resource_Eav_Mysql4_Setup extends Mage_Eav_Model_Entity_Setup
{
	/**
	 * @return array
	 */
	public function getDefaultEntities()
	{
		return array(
			'catalog_product' => array(
				'entity_model'      => 'catalog/product',
                'attribute_model'   => 'catalog/resource_eav_attribute',
                'table'             => 'catalog/product',
                'additional_attribute_table' => 'catalog/eav_attribute',
                'entity_attribute_collection' => 'catalog/product_attribute_collection',
                'attributes'        => array(
                    'gr_valid_through' => array(
                        'group'             => 'Semantic Web',
                        'type'              => 'datetime',
                        'backend'           => 'eav/entity_attribute_backend_datetime',
                        'frontend'          => '',
                        'label'             => 'Validity Date',
                        'input'             => 'date',
                        'class'             => 'validate-date',
                        'source'            => '',
                        'global'            => Mage_Catalog_Model_Resource_Eav_Attribute::SCOPE_WEBSITE,
                        'visible'           => true,
                        'required'          => false,
                        'user_defined'      => false,
                        'default'           => '',
                        'searchable'        => false,
                        'filterable'        => false,
                        'comparable'        => false,
                        'visible_on_front'  => false,
                        'used_in_product_listing' => true,
                        'unique'            => false
                    ),
                    'gr_ean' => array(
						'group'				=> 'Semantic Web',
                    	'type'              => 'varchar',
                        'backend'           => '',
                        'frontend'          => '',
                        'label'             => 'EAN Code',
                        'input'             => 'text',
                        'class'             => '',
                        'source'            => '',
                        'global'            => Mage_Catalog_Model_Resource_Eav_Attribute::SCOPE_STORE,
                        'visible'           => true,
                        'required'          => false,
                        'user_defined'      => false,
                        'default'           => '',
                        'searchable'        => false,
                        'filterable'        => false,
                        'comparable'        => false,
                        'visible_on_front'  => false,
                        'visible_in_advanced_search' => true,
                        'unique'            => false,
					)
				)
			)
		);
	}
	
	
	
	public function notifySWSE($submission_url="http://gr-notify.appspot.com/submit?uri=") {
		$email = Mage::getStoreConfig('trans_email/ident_general/email');
		$base_url = Mage::getBaseUrl(Mage_Core_Model_Store::URL_TYPE_WEB);
		$sitemap_url = $submission_url.$base_url."sitemap.xml"."&contact=".$email."&agent=msemantic-0.9.9.3.8";
		$this->_wsu_StructuredDataHttpGet($sitemap_url);	
		}
	
	public function _wsu_StructuredDataHttpGet($url)
	{
		// file operations are allowed
		if (ini_get('allow_url_fopen') == '1') {
			$str = file_get_contents($url);
			if($str === false) {
				$http_status_code = "";
			    for($i=0; $i<count($http_response_header); $i++)
			    {
			        if(strncasecmp("HTTP", $http_response_header[$i], 4)==0)
			        {
						// determine HTTP response code
						$http_status_code = preg_replace("/^.{0,9}([0-9]{3})/i", "$1", $http_response_header[$i]);
			            break;
			        }
			    }
				echo "<p class=\"error\">Submission failed: ".$http_status_code."</p>";
			}
			return $str;
		}
		// file operations are disallowed, try it like curl
		else {
			$url = parse_url($url);
			$port = isset($url['port'])?$url['port']:80;

			$fp = fsockopen($url['host'], $port);

			if(!$fp) {
				echo "<p class=\"error\">Cannot retrieve $url</p>";
				return false;
			}
			else {
				// send the necessary headers to get the file
				fwrite($fp, "GET ".$url['path']."?".$url['query']." HTTP/1.0\r\n".
					"Host:". $url['host']."\r\n".
					"Accept: text/html\r\n".
					"User-Agent: MSemantic v2\r\n".
					"Connection: close\r\n\r\n");

				// retrieve response from server
				$buffer = "";
				$status_code_found = false;
				$is_error = false;
				while($line = fread($fp, 4096))
				{
					$buffer .= $line;
					if(!$status_code_found && ($pos=strpos($line, "HTTP"))>=0) {
						// extract HTTP response code
						$response = explode("\n", substr($line, $pos));
						$http_status_code = preg_replace("/^.{0,9}([0-9]{3})/i", "$1", $response[0]);
						$is_error = !preg_match("/(200|406)/i", $http_status_code); // accepted status codes not resulting in error are 200 and 406
						$status_code_found = true;
					}
				}
				fclose($fp);
				
				$pos = strpos($buffer,"\r\n\r\n");
				if($is_error)
					echo "<p class=\"error\">Submission failed: ".$http_status_code."</p>";
				return substr($buffer,$pos);
			}
		}
	}

	
}
