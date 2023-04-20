<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Department>
 */
class DepartmentFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        $array_dept = [
            'CS' => 'Computer Science',
            'IT' => 'Information Technology',
            'IS' => 'Information Systems',
        ];
        $dept_code = fake()->unique()->randomElement(array_keys($array_dept));
        return [
            'dept_code' => $dept_code,
            'name' => $array_dept[$dept_code],
        ];
    }
}
