<?php

/**
 * Diggin - Simplicity PHP Library
 * 
 * LICENSE
 *
 * This source file is subject to the new BSD license.
 * It is also available through the world-wide-web at this URL:
 * http://diggin.musicrider.com/LICENSE
 * 
 * @category   Diggin
 * @package    Diggin_Tool
 * @subpackage Framework
 * @copyright  2006-2010 sasezaki (http://diggin.musicrider.com)
 * @license    http://diggin.musicrider.com/LICENSE     New BSD License
 */

/**
 * @namespace
 */
namespace Diggin\Tool\Framework\Client\Console;

/**
 * @category   Diggin
 * @package    Diggin_Tool
 * @copyright  2006-2010 sasezaki (http://diggin.musicrider.com)
 * @license    http://diggin.musicrider.com/LICENSE     New BSD License
 * 
 * This code was mostly adapted from Zend_Tool_Framework_Client_Console_HelpSystem - a part of the Zend Framework
 * Copyright (c) 2005-2010 Zend Technologies USA Inc., licensed under the
 * New BSD License. See http://framework.zend.com for more information.
 */
class HelpSystem extends \Zend\Tool\Framework\Client\Console\HelpSystem
{
    private $_shName;

    public function setShName($shName)
    {
        $this->_shName = $shName;
    }

    /**
     * respondWithGeneralHelp()
     *
     * @return Diggin_Tool_Framework_Client_Console_HelpSystem
     */
    public function respondWithGeneralHelp()
    {
        $this->_respondWithHeader();

        $noSeparator = array('separator' => false);

        $shName = $this->_shName;
        $text = sprintf('    Example: "%s ? version" will list all available actions for the version provider.', $this->_shName);

        $this->_response->appendContent('Usage:', array('color' => 'green'))
            ->appendContent('    ', $noSeparator)
            //->appendContent('zf', array_merge(array('color' => 'cyan'), $noSeparator))
            ->appendContent($shName, array_merge(array('color' => 'cyan'), $noSeparator))
            ->appendContent(' [--global-opts]', $noSeparator)
            ->appendContent(' action-name', array_merge(array('color' => 'cyan'), $noSeparator))
            ->appendContent(' [--action-opts]', $noSeparator)
            ->appendContent(' provider-name', array_merge(array('color' => 'cyan'), $noSeparator))
            ->appendContent(' [--provider-opts]', $noSeparator)
            ->appendContent(' [provider parameters ...]')
            ->appendContent('    Note: You may use "?" in any place of the above usage string to ask for more specific help information.', array('color'=>'yellow'))
            //->appendContent('    Example: "zf ? version" will list all available actions for the version provider.', 
            ->appendContent($text, 
                            array('color'=>'yellow', 'separator' => 2))
            ->appendContent('Providers and their actions:', array('color' => 'green'));

        $this->_respondWithSystemInformation();
        return $this;
    }

    /**
     * _respondWithHeader()
     *
     * @return Zend_Tool_Framework_Client_Console_HelpSystem
     */
    protected function _respondWithHeader()
    {
        /**
         * @see Zend_Version
         */
        require_once 'Zend/Version.php';
        //$this->_response->appendContent('Zend Framework', array('color' => array('hiWhite'), 'separator' => false));
        //$this->_response->appendContent(' Command Line Console Tool v' . Zend_Version::VERSION . '');
        $this->_response->appendContent(strtoupper($this->_shName), array('color' => array('hiWhite'), 'separator' => false));
        $this->_response->appendContent(' Command Line Console Tool with ZF v' . \Zend\Version::VERSION . '');
        return $this;
    }

    protected function _respondWithSystemInformation($providerNameFilter = null, $actionNameFilter = null, $includeAllSpecialties = false)
    {
        $manifest = $this->_registry->getManifestRepository();

        $providerMetadatasSearch = array(
            'type'       => 'Tool',
            'name'       => 'providerName',
            'clientName' => 'console'
            );

        if (is_string($providerNameFilter)) {
            $providerMetadatasSearch = array_merge($providerMetadatasSearch, array('providerName' => $providerNameFilter));
        }

        $actionMetadatasSearch = array(
            'type'       => 'Tool',
            'name'       => 'actionName',
            'clientName' => 'console'
            );

        if (is_string($actionNameFilter)) {
            $actionMetadatasSearch = array_merge($actionMetadatasSearch, array('actionName' => $actionNameFilter));
        }

        // get the metadata's for the things to display
        $displayProviderMetadatas = $manifest->getMetadatas($providerMetadatasSearch);
        $displayActionMetadatas = $manifest->getMetadatas($actionMetadatasSearch);

        // create index of actionNames
        for ($i = 0; $i < count($displayActionMetadatas); $i++) {
            $displayActionNames[] = $displayActionMetadatas[$i]->getActionName();
        }

        foreach ($displayProviderMetadatas as $providerMetadata) {

            $providerNameDisplayed = false;

            $providerName = $providerMetadata->getProviderName();
            $providerSignature = $providerMetadata->getReference();

            foreach ($providerSignature->getActions() as $actionInfo) {

                $actionName = $actionInfo->getName();

                // check to see if this action name is valid
                if (($foundActionIndex = array_search($actionName, $displayActionNames)) === false) {
                    continue;
                } else {
                    $actionMetadata = $displayActionMetadatas[$foundActionIndex];
                }

                $specialtyMetadata = $manifest->getMetadata(array(
                    'type'          => 'Tool',
                    'name'          => 'specialtyName',
                    'providerName'  => $providerName,
                    'specialtyName' => '_Global',
                    'clientName'    => 'console'
                    ));

                // lets do the main _Global action first
                $actionableGlobalLongParamMetadata = $manifest->getMetadata(array(
                    'type'          => 'Tool',
                    'name'          => 'actionableMethodLongParams',
                    'providerName'  => $providerName,
                    'specialtyName' => '_Global',
                    'actionName'    => $actionName,
                    'clientName'    => 'console'
                    ));

                $actionableGlobalMetadatas = $manifest->getMetadatas(array(
                    'type'          => 'Tool',
                    'name'          => 'actionableMethodLongParams',
                    'providerName'  => $providerName,
                    'actionName'    => $actionName,
                    'clientName'    => 'console'
                    ));

                if ($actionableGlobalLongParamMetadata) {

                    if (!$providerNameDisplayed) {
                        $this->_respondWithProviderName($providerMetadata);
                        $providerNameDisplayed = true;
                    }

                    $this->_respondWithCommand($providerMetadata, $actionMetadata, $specialtyMetadata, $actionableGlobalLongParamMetadata);

                    $actionIsGlobal = true;
                } else {
                    $actionIsGlobal = false;
                }

                // check for providers without a _Global action
                $isSingleSpecialProviderAction = false;
                if (!$actionIsGlobal && count($actionableGlobalMetadatas) == 1) {
                    $isSingleSpecialProviderAction = true;
                    $this->_respondWithProviderName($providerMetadata);
                    $providerNameDisplayed = true;
                }
                
                if ($includeAllSpecialties || $isSingleSpecialProviderAction) {

                    foreach ($providerSignature->getSpecialties() as $specialtyName) {

                        if ($specialtyName == '_Global') {
                            continue;
                        }

                        $specialtyMetadata = $manifest->getMetadata(array(
                            'type'          => 'Tool',
                            'name'          => 'specialtyName',
                            'providerName'  => $providerMetadata->getProviderName(),
                            'specialtyName' => $specialtyName,
                            'clientName'    => 'console'
                            ));

                        $actionableSpecialtyLongMetadata = $manifest->getMetadata(array(
                            'type'          => 'Tool',
                            'name'          => 'actionableMethodLongParams',
                            'providerName'  => $providerMetadata->getProviderName(),
                            'specialtyName' => $specialtyName,
                            'actionName'    => $actionName,
                            'clientName'    => 'console'
                            ));

                        if($actionableSpecialtyLongMetadata) {
                            $this->_respondWithCommand($providerMetadata, $actionMetadata, $specialtyMetadata, $actionableSpecialtyLongMetadata);
                        }

                    }
                }
                
                // reset the special flag for single provider action with specialty
                $isSingleSpecialProviderAction = false;

                $shName = $this->_shName;

                if (!$includeAllSpecialties && count($actionableGlobalMetadatas) > 1) {
                    $this->_response->appendContent('    Note: There are specialties, use ', array('color' => 'yellow', 'separator' => false));
                    $this->_response->appendContent(
                        "$shName " . $actionMetadata->getValue() . ' ' . $providerMetadata->getValue() . '.?',
                        array('color' => 'cyan', 'separator' => false)
                        );
                    $this->_response->appendContent(' to get specific help on them.', array('color' => 'yellow'));
                }

            }

            if ($providerNameDisplayed) {
                $this->_response->appendContent(null, array('separator' => true));
            }
        }
        return $this;
    }

    /**
     * _respondWithCommand()
     *
     * @param Zend_Tool_Framework_Metadata_Tool $providerMetadata
     * @param Zend_Tool_Framework_Metadata_Tool $actionMetadata
     * @param Zend_Tool_Framework_Metadata_Tool $specialtyMetadata
     * @param Zend_Tool_Framework_Metadata_Tool $parameterLongMetadata
     * @return Zend_Tool_Framework_Client_Console_HelpSystem
     */
    protected function _respondWithCommand(
        \Zend\Tool\Framework\Metadata\Tool $providerMetadata,
        \Zend\Tool\Framework\Metadata\Tool $actionMetadata,
        \Zend\Tool\Framework\Metadata\Tool $specialtyMetadata,
        \Zend\Tool\Framework\Metadata\Tool $parameterLongMetadata)//,
        //Zend_Tool_Framework_Metadata_Tool $parameterShortMetadata)
    {
      
        $shName = $this->_shName;
        $this->_response->appendContent(
            "    $shName " . $actionMetadata->getValue() . ' ' . $providerMetadata->getValue(),
            array('color' => 'cyan', 'separator' => false)
            );

        if ($specialtyMetadata->getSpecialtyName() != '_Global') {
            $this->_response->appendContent('.' . $specialtyMetadata->getValue(), array('color' => 'cyan', 'separator' => false));
        }

        foreach ($parameterLongMetadata->getValue() as $paramName => $consoleParamName) {
            $methodInfo = $parameterLongMetadata->getReference();
            $paramString = ' ' . $consoleParamName;
            if ( ($defaultValue = $methodInfo['parameterInfo'][$paramName]['default']) != null) {
                $paramString .= '[=' . $defaultValue . ']';
            }
            $this->_response->appendContent($paramString . '', array('separator' => false));
        }

       $this->_response->appendContent(null, array('separator' => true));
       return $this;
    }

}
