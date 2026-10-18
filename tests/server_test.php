<?php
// This file is part of Moodle - http://moodle.org/
//
// Moodle is free software: you can redistribute it and/or modify
// it under the terms of the GNU General Public License as published by
// the Free Software Foundation, either version 3 of the License, or
// (at your option) any later version.
//
// Moodle is distributed in the hope that it will be useful,
// but WITHOUT ANY WARRANTY; without even the implied warranty of
// MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
// GNU General Public License for more details.
//
// You should have received a copy of the GNU General Public License
// along with Moodle.  If not, see <http://www.gnu.org/licenses/>.

namespace webservice_restful;

defined('MOODLE_INTERNAL') || die();
global $CFG;

require_once($CFG->dirroot . '/webservice/restful/locallib.php');

/**
 * Restful server testcase.
 *
 * @package    webservice_restful
 * @copyright  Matt Porritt <mattp@catalyst-au.net>
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
class server_test extends \advanced_testcase {

    /**
     * Test get header method extracts HTTP headers.
     *
     * @covers ::get_headers()
     */
    public function test_get_headers(): void {
        $headers = [
            'USER' => 'www-data',
            'HOME' => '/var/www',
            'HTTP_CONTENT_LENGTH' => '17',
            'HTTP_AUTHORIZATION' => 'e71561c88ca7f0f0c94fee66ca07247b',
            'HTTP_ACCEPT' => 'application/json',
            'HTTP_CONTENT_TYPE' => 'application/x-www-form-urlencoded',
            'HTTP_USER_AGENT' => 'curl/7.47.0',
            'HTTP_HOST' => 'moodle.local',
            'REDIRECT_STATUS' => '200',
            'SERVER_NAME' => 'moodle.local',
            'SERVER_PORT' => '80',
            'SERVER_ADDR' => '192.168.56.103',
            'REMOTE_PORT' => '39402',
            'REMOTE_ADDR' => '192.168.56.1',
        ];
        $expected = [
            'HTTP_CONTENT_LENGTH' => '17',
            'HTTP_AUTHORIZATION' => 'e71561c88ca7f0f0c94fee66ca07247b',
            'HTTP_ACCEPT' => 'application/json',
            'HTTP_CONTENT_TYPE' => 'application/x-www-form-urlencoded',
            'HTTP_USER_AGENT' => 'curl/7.47.0',
            'HTTP_HOST' => 'moodle.local',
        ];

        $builder = $this->getMockBuilder('webservice_restful_server');
        $builder->disableOriginalConstructor();
        $stub = $builder->getMock();

        // We're testing a private method, so we need to setup reflector magic.
        $method = new \ReflectionMethod('webservice_restful_server', 'get_headers');
        $method->setAccessible(true); // Allow accessing of private method.
        $proxy = $method->invoke($stub, $headers); // Get result of invoked method.

        $this->assertEquals($expected, $proxy);
    }

    /**
     * Test get wstoken method extracts token.
     *
     * @covers ::get_wstoken()
     */
    public function test_get_wstoken(): void {
        $headers = [
            'HTTP_AUTHORIZATION' => 'e71561c88ca7f0f0c94fee66ca07247b',
            'HTTP_ACCEPT' => 'application/json',
            'HTTP_CONTENT_TYPE' => 'application/x-www-form-urlencoded',
        ];
        $expected = 'e71561c88ca7f0f0c94fee66ca07247b';

        $builder = $this->getMockBuilder('webservice_restful_server');
        $builder->disableOriginalConstructor();
        $stub = $builder->getMock();

        // We're testing a private method, so we need to setup reflector magic.
        $method = new \ReflectionMethod('webservice_restful_server', 'get_wstoken');
        $method->setAccessible(true); // Allow accessing of private method.
        $proxy = $method->invoke($stub, $headers); // Get result of invoked method.

        $this->assertEquals($expected, $proxy);
    }

    /**
     * Test get wstoken method correctly errors.
     *
     * @covers ::get_wstoken()
     */
    public function test_get_wstoken_error(): void {
        $headers = [];

        // Capture the output instead of expecting a specific string
        ob_start();

        // We're testing a private method, so we need to setup reflector magic.
        $method = new \ReflectionMethod('webservice_restful_server', 'get_wstoken');
        $method->setAccessible(true); // Allow accessing of private method.
        $proxy = $method->invoke(new \webservice_restful_server(WEBSERVICE_AUTHMETHOD_PERMANENT_TOKEN), $headers);

        $output = ob_get_clean();

        // Parse JSON and verify individual components
        $decoded = json_decode($output, true);

        $this->assertIsArray($decoded, 'Output should be valid JSON');
        $this->assertStringEndsWith('moodle_exception', $decoded['exception']);
        $this->assertEquals('noauthheader', $decoded['errorcode']);
        $this->assertEquals('No Authorization header found in request sent to Moodle', $decoded['message']);
    }

    /**
     * Test get wsfunction method extracts function.
     *
     * @covers ::get_wsfunction()
     */
    public function test_get_wsfunction(): void {
        $getvars = ['file' => '/core_course_get_courses'];
        $expected = 'core_course_get_courses';

        $builder = $this->getMockBuilder('webservice_restful_server');
        $builder->disableOriginalConstructor();
        $stub = $builder->getMock();

        // We're testing a private method, so we need to setup reflector magic.
        $method = new \ReflectionMethod('webservice_restful_server', 'get_wsfunction');
        $method->setAccessible(true); // Allow accessing of private method.
        $proxy = $method->invoke($stub, $getvars); // Get result of invoked method.

        $this->assertEquals($expected, $proxy);
    }

    /**
     * Test get wsfunction method correctly errors.
     *
     * @covers ::get_wsfunction()
     */
    public function test_get_wsfunction_error(): void {
        $getvars = [];

        // Capture the output instead of expecting a specific string
        ob_start();

        // We're testing a private method, so we need to setup reflector magic.
        $method = new \ReflectionMethod('webservice_restful_server', 'get_wsfunction');
        $method->setAccessible(true); // Allow accessing of private method.
        $proxy = $method->invoke(new \webservice_restful_server(WEBSERVICE_AUTHMETHOD_PERMANENT_TOKEN), $getvars);

        $output = ob_get_clean();

        // Parse JSON and verify individual components
        $decoded = json_decode($output, true);

        $this->assertIsArray($decoded, 'Output should be valid JSON');
        $this->assertStringEndsWith('moodle_exception', $decoded['exception']);
        $this->assertEquals('nowsfunction', $decoded['errorcode']);
        $this->assertEquals('No webservice function found in URL sent to Moodle', $decoded['message']);
    }

    /**
     * Test get response format method extracts response format.
     *
     * @covers ::get_responseformat()
     */
    public function test_get_responseformat(): void {
        $headers = [
            'HTTP_AUTHORIZATION' => 'e71561c88ca7f0f0c94fee66ca07247b',
            'HTTP_ACCEPT' => 'application/json',
            'HTTP_CONTENT_TYPE' => 'application/xml',
        ];
        $expected = 'json';

        $builder = $this->getMockBuilder('webservice_restful_server');
        $builder->disableOriginalConstructor();
        $stub = $builder->getMock();

        // We're testing a private method, so we need to setup reflector magic.
        $method = new \ReflectionMethod('webservice_restful_server', 'get_responseformat');
        $method->setAccessible(true); // Allow accessing of private method.
        $proxy = $method->invoke($stub, $headers); // Get result of invoked method.

        $this->assertEquals($expected, $proxy);
    }

    /**
     * Test get response format method correctly errors.
     *
     * @covers ::get_responseformat()
     */
    public function test_get_responseformat_error(): void {
        $headers = [];

        // Capture the output instead of expecting a specific string
        ob_start();

        // We're testing a private method, so we need to setup reflector magic.
        $method = new \ReflectionMethod('webservice_restful_server', 'get_responseformat');
        $method->setAccessible(true); // Allow accessing of private method.
        $proxy = $method->invoke(new \webservice_restful_server(WEBSERVICE_AUTHMETHOD_PERMANENT_TOKEN), $headers);

        $output = ob_get_clean();

        // Parse JSON and verify individual components
        $decoded = json_decode($output, true);

        $this->assertIsArray($decoded, 'Output should be valid JSON');
        $this->assertStringEndsWith('moodle_exception', $decoded['exception']);
        $this->assertEquals('noacceptheader', $decoded['errorcode']);
        $this->assertEquals('No Accept header found in request sent to Moodle', $decoded['message']);
    }

    /**
     * Test get request format method extracts request format.
     *
     * @covers ::get_requestformat()
     */
    public function test_get_requestformat(): void {
        $headers = [
            'HTTP_AUTHORIZATION' => 'e71561c88ca7f0f0c94fee66ca07247b',
            'HTTP_ACCEPT' => 'application/json',
            'HTTP_CONTENT_TYPE' => 'application/xml',
        ];
        $expected = 'xml';

        $builder = $this->getMockBuilder('webservice_restful_server');
        $builder->disableOriginalConstructor();
        $stub = $builder->getMock();

        // We're testing a private method, so we need to setup reflector magic.
        $method = new \ReflectionMethod('webservice_restful_server', 'get_requestformat');
        $method->setAccessible(true); // Allow accessing of private method.
        $proxy = $method->invoke($stub, $headers); // Get result of invoked method.

        $this->assertEquals($expected, $proxy);
    }

    /**
     * Test get request format method correctly errors.
     *
     * @covers ::get_requestformat()
     */
    public function test_get_requestformat_error(): void {
        $headers = [];

        // Capture the output instead of expecting a specific string
        ob_start();

        // We're testing a private method, so we need to setup reflector magic.
        $method = new \ReflectionMethod('webservice_restful_server', 'get_requestformat');
        $method->setAccessible(true); // Allow accessing of private method.
        $proxy = $method->invoke(new \webservice_restful_server(WEBSERVICE_AUTHMETHOD_PERMANENT_TOKEN), $headers);

        $output = ob_get_clean();

        // Parse JSON and verify individual components
        $decoded = json_decode($output, true);

        $this->assertIsArray($decoded, 'Output should be valid JSON');
        $this->assertStringEndsWith('moodle_exception', $decoded['exception']);
        $this->assertEquals('notypeheader', $decoded['errorcode']);
        $this->assertEquals('No Content Type header found in request sent to Moodle', $decoded['message']);
    }

    /**
     * Test the request parameters are not parsed when the token is invalid.
     *
     * @covers \webservice_restful_server::run
     */
    public function test_run_invalid_token_does_not_parse_parameters(): void {
        $this->resetAfterTest();

        $_SERVER['HTTP_AUTHORIZATION'] = 'not-a-real-token';
        $_SERVER['HTTP_ACCEPT'] = 'application/json';
        $_SERVER['HTTP_CONTENT_TYPE'] = 'application/json';
        $_SERVER['PATH_INFO'] = '/core_webservice_get_site_info';
        $_GET['serviceshortnames'] = ['restfultest'];

        $server = new \webservice_restful_server(WEBSERVICE_AUTHMETHOD_PERMANENT_TOKEN);
        try {
            $server->run();
            $this->fail('An invalid token must be rejected.');
        } catch (\moodle_exception $e) {
            $this->assertEquals('invalidtoken', $e->errorcode);
        } finally {
            restore_exception_handler();
        }

        $parameters = new \ReflectionProperty($server, 'parameters');
        $this->assertNull($parameters->getValue($server));
    }

    /**
     * Test the request parameters are parsed and executed when the token is valid.
     *
     * @covers \webservice_restful_server::run
     */
    public function test_run_valid_token_parses_parameters(): void {
        $this->resetAfterTest();

        $generator = $this->getDataGenerator()->get_plugin_generator('core_webservice');
        $service = $generator->create_service(['name' => 'Restful test', 'shortname' => 'restfultest', 'enabled' => 1]);
        $generator->create_service_functions(['service' => 'restfultest', 'functions' => 'core_webservice_get_site_info']);
        $token = \core_external\util::generate_token(
            EXTERNAL_TOKEN_PERMANENT,
            $service,
            get_admin()->id,
            \context_system::instance()
        );

        $_SERVER['HTTP_AUTHORIZATION'] = 'Bearer ' . $token;
        $_SERVER['HTTP_ACCEPT'] = 'application/json';
        $_SERVER['HTTP_CONTENT_TYPE'] = 'application/json';
        $_SERVER['PATH_INFO'] = '/core_webservice_get_site_info';
        $_GET['serviceshortnames'] = ['restfultest'];

        // Stop before the response is sent, run() ends with die.
        $server = new class (WEBSERVICE_AUTHMETHOD_PERMANENT_TOKEN) extends \webservice_restful_server {
            /**
             * Interrupt the request instead of sending the response.
             */
            protected function send_response() {
                throw new \RuntimeException('send_response');
            }
        };
        try {
            $server->run();
            $this->fail('The response must be reached.');
        } catch (\RuntimeException $e) {
            $this->assertEquals('send_response', $e->getMessage());
        } finally {
            restore_exception_handler();
        }

        $parameters = new \ReflectionProperty($server, 'parameters');
        $this->assertEquals(['serviceshortnames' => ['restfultest']], $parameters->getValue($server));
        $returns = new \ReflectionProperty($server, 'returns');
        $this->assertEquals('admin', $returns->getValue($server)['username']);
    }
}
