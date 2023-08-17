<?php

/*
 * This file is part of the Qsnh/meedu.
 *
 * (c) 杭州白书科技有限公司
 */

namespace Tests\API\Frontend\V2;

use Tests\API\Frontend\Base;
use App\Services\Course\Models\CourseCategory;

class CourseCategoriesTest extends Base
{
    public function test_courses()
    {
        CourseCategory::factory()->count(10)->create(['is_show' => 1]);
        $response = $this->get('/api/v2/course_categories');
        $r = $this->assertResponseSuccess($response);
        $this->assertEquals(10, count($r['data']));
    }
}
