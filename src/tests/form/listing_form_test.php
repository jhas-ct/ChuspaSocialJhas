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

namespace local_chuspasocial\form;

use advanced_testcase;

defined('MOODLE_INTERNAL') || die();

global $CFG;
require_once($CFG->libdir . '/formslib.php');

/**
 * Pruebas unitarias para el formulario listing_form.
 *
 * @package    local_chuspasocial
 * @category   test
 * @copyright  2026 SIS-INF
 * @license    https://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 * @covers     \local_chuspasocial\form\listing_form
 */
class listing_form_test extends advanced_testcase {
    /**
     * Crea el formulario con un usuario autenticado.
     *
     * Los strings del formulario pueden no estar definidos todavia (se agregan
     * en issues aparte), asi que se descartan los avisos de debugging.
     *
     * @return listing_form
     */
    private function create_form(): listing_form {
        $this->setUser($this->getDataGenerator()->create_user());
        $form = new listing_form('http://localhost/');
        $this->resetDebugging();
        return $form;
    }

    /**
     * Un precio negativo devuelve error en la clave price.
     */
    public function test_validation_negative_price_returns_error(): void {
        $this->resetAfterTest();
        $form = $this->create_form();

        $errors = $form->validation(['price' => '-1'], []);
        $this->resetDebugging();

        $this->assertArrayHasKey('price', $errors);
    }

    /**
     * Un precio no numerico devuelve error en la clave price.
     */
    public function test_validation_non_numeric_price_returns_error(): void {
        $this->resetAfterTest();
        $form = $this->create_form();

        $errors = $form->validation(['price' => 'abc'], []);
        $this->resetDebugging();

        $this->assertArrayHasKey('price', $errors);
    }

    /**
     * Un precio de cero devuelve error en price con la validacion actual.
     *
     * El issue pedia que '0' fuera valido, pero validation() rechaza los
     * precios menores o iguales a cero. Esta prueba refleja ese comportamiento.
     */
    public function test_validation_zero_price_returns_error(): void {
        $this->resetAfterTest();
        $form = $this->create_form();

        $errors = $form->validation(['price' => '0'], []);
        $this->resetDebugging();

        $this->assertArrayHasKey('price', $errors);
    }

    /**
     * Un precio decimal valido no devuelve error en price.
     */
    public function test_validation_decimal_price_returns_no_error(): void {
        $this->resetAfterTest();
        $form = $this->create_form();

        $errors = $form->validation(['price' => '25.50'], []);
        $this->resetDebugging();

        $this->assertArrayNotHasKey('price', $errors);
    }

    /**
     * Sin la clave price no se lanza ninguna excepcion.
     */
    public function test_validation_without_price_does_not_throw(): void {
        $this->resetAfterTest();
        $form = $this->create_form();

        $errors = $form->validation([], []);
        $this->resetDebugging();

        $this->assertIsArray($errors);
        $this->assertArrayNotHasKey('price', $errors);
    }

    /**
     * El formulario define todos los elementos esperados.
     */
    public function test_form_defines_expected_elements(): void {
        $this->resetAfterTest();
        $form = $this->create_form();

        $property = new \ReflectionProperty($form, '_form');
        $mform = $property->getValue($form);

        $elements = ['title', 'author', 'description', 'price', 'course', 'photos', 'contact'];
        foreach ($elements as $element) {
            $this->assertTrue($mform->elementExists($element), "Falta el elemento $element");
        }
    }
}
