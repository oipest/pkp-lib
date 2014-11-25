<?php

/**
 * @file tests/functional/workflow/PKPProductionBaseTestCase.inc.php
 *
 * Copyright (c) 2014 Simon Fraser University Library
 * Copyright (c) 2000-2014 John Willinsky
 * Distributed under the GNU GPL v2. For full terms see the file docs/COPYING.
 *
 * @class PKPProductionBaseTestCase
 * @ingroup tests_functional_workflow
 *
 * @brief Base class for production tests.
 */

import('lib.pkp.tests.functional.workflow.WorkflowBaseTestCase');

class PKPProductionBaseTestCase extends WorkflowBaseTestCase {

	/**
	 * Test production.
	 */
	public function testProduction() {
		$this->open(self::$baseUrl);

		$emailLog = PluginRegistry::getPlugin('generic', 'emailloggerplugin'); 
		$submissionId = $this->findSubmissionAsEditor('dbarnes', null, $fullTitle = 'Wild Words: Essays on Alberta Literature');

		$notificationMessages = $this->getNotificationMessages();
		$this->waitForElementPresent('css=[id^=component-grid-catalogentry-publicationformatgrid-]');
		$this->assertTextPresent($notificationMessages[NOTIFICATION_TYPE_APPROVE_SUBMISSION]);

		$this->addPublicationFormat($formatTitle = 'PDF production test');

		$this->openPublicationFormatTab($formatTitle);
		$this->assertTextPresent($notificationMessages[NOTIFICATION_TYPE_FORMAT_NEEDS_APPROVED_SUBMISSION]);

		// Approve submission.
		$this->click('css=a[href=#submission]');
		$this->waitForElementPresent($selector = 'css=input#confirm');
		$this->click($selector);
		$this->click('css=#submission button[id^=submitFormButton-]');
		$this->waitJQuery();
		$this->click('css=.pkp_controllers_modal_titleBar span.xIcon');

		// This is necessary to handle bug #9023, when fixed remove it.
		$this->open(self::$baseUrl . '/index.php/publicknowledge/workflow/access/' . $submissionId);

		$this->assertTextPresent($notificationMessages[NOTIFICATION_TYPE_VISIT_CATALOG]);
		$this->assertTextNotPresent($notificationMessages[NOTIFICATION_TYPE_APPROVE_SUBMISSION]);

		$this->openPublicationFormatTab($formatTitle);
		$this->assertTextNotPresent($notificationMessages[NOTIFICATION_TYPE_FORMAT_NEEDS_APPROVED_SUBMISSION]);
		$this->logOut();
	}
	
	/**
	 * Get notification message texts indexed by notification type.
	 * @return array
	 */
	protected function getNotificationMessages() {
		return array();
	}
}
