<?php
use App\Jobs\CheckRecurringContribution;
use App\RecurringContribution;
use Carbon\Carbon;

/**
 * @author: chengxian
 * Date: 5/3/16
 * @copyright Cheng Xian Lim
 */
class RecurringContributionsIntegrationTest extends TestCase
{
    public function setUp()
    {
        parent::setUp();

        $this->artisan('migrate:reset', [
            '--database' => 'sqlite_test'
        ]);

        $this->artisan('migrate', [
            '--database' => 'sqlite_test',
            '--path' => $this->getMigrationsDir()
        ]);
    }

    public function tearDown()
    {
        $this->artisan('migrate:reset', [
            '--database' => 'sqlite_test'
        ]);
        parent::tearDown();
    }

    /**
     * @return RecurringContribution
     */
    public function getRecurringContribution()
    {
        $r = new RecurringContribution();
        $r->start_date = new Carbon('July 2, 2015');
        $r->user_id = 0;
        $r->child_id = 0;
        $r->amount = 100;

        return $r;
    }

    /**
     * @return Carbon
     */
    public function getNow()
    {
        return new Carbon('July 2, 2015');
    }

    /** @test */
    public function it_saves_daily()
    {
        $r = $this->getRecurringContribution();
        $r->recurring_type = 'daily';
        $r->save();

        $count = RecurringContribution::count();
        $this->assertGreaterThan(0, $count);
    }

    /** @test */
    public function it_saves_weekly()
    {
        $r = $this->getRecurringContribution();
        $r->recurring_type = 'weekly';
        $r->save();

        $expectedDayOfWeek = 4;
        $this->seeInDatabase('recurring_contributions_schedule', ['day_of_week' => $expectedDayOfWeek]);
    }

    /** @test */
    public function it_saves_monthly()
    {
        $r = $this->getRecurringContribution();
        $r->recurring_type = 'monthly';
        $r->save();

        $expectedDayOfMonth = 2;
        $this->seeInDatabase('recurring_contributions_schedule', ['day_of_month' => $expectedDayOfMonth]);
    }

    /** @test */
    public function it_saves_yearly()
    {
        $r = $this->getRecurringContribution();
        $r->recurring_type = 'yearly';
        $r->save();

        $expectedDayOfYear = 182;
        $this->seeInDatabase('recurring_contributions_schedule', ['day_of_year' => $expectedDayOfYear]);
    }

    /** @test */
    public function it_should_spawn_daily()
    {
        $r = $this->getRecurringContribution();
        $r->recurring_type = 'daily';
        $shouldSpawn = $r->shouldSpawn($this->getNow());

        $this->assertTrue($shouldSpawn);
    }

    /** @test */
    public function it_should_spawn_weekly1()
    {
        $r = $this->getRecurringContribution();
        $r->recurring_type = 'weekly';
        $r->save();

        $shouldSpawn = $r->shouldSpawn($this->getNow()->addDay(7));
        $this->assertTrue($shouldSpawn);
    }

    /** @test */
    public function it_should_spawn_weekly2()
    {
        $r = $this->getRecurringContribution();
        $r->recurring_type = 'weekly';
        $r->save();

        $shouldSpawn = $r->shouldSpawn($this->getNow()->addDay(364));
        $this->assertTrue($shouldSpawn);
    }

    /** @test */
    public function it_should_spawn_weekly4()
    {
        $r = $this->getRecurringContribution();
        $r->recurring_type = 'weekly';
        $r->save();

        $shouldSpawn = $r->shouldSpawn($this->getNow()->addDay(14));
        $this->assertTrue($shouldSpawn);
    }

    /** @test */
    public function it_should_spawn_weekly3()
    {
        $r = $this->getRecurringContribution();
        $r->recurring_type = 'weekly';
        $r->save();

        $shouldSpawn = $r->shouldSpawn($this->getNow()->addYear(2)->addDay(1));
        $this->assertFalse($shouldSpawn);
    }

    /** @test */
    public function it_should_spawn_weekly5()
    {
        $r = $this->getRecurringContribution();
        $r->recurring_type = 'weekly';
        $r->save();

        $shouldSpawn = $r->shouldSpawn($this->getNow()->addDay(11));
        $this->assertFalse($shouldSpawn);
    }

    /** @test */
    public function it_should_spawn_monthly()
    {
        $r = $this->getRecurringContribution();
        $r->recurring_type = 'monthly';
        $r->start_date = new Carbon('July 31, 2015');
        $r->save();
        $day = new Carbon("Sept 2, 2016");
        $shouldSpawn = $r->shouldSpawn($day);
        $this->assertTrue($shouldSpawn);
    }

    /** @test */
    public function it_should_spawn_monthly2()
    {
        $r = $this->getRecurringContribution();
        $r->recurring_type = 'monthly';
        $r->start_date = new Carbon('July 31, 2015');
        $r->save();
        $day = new Carbon("June 30, 2016");
        $shouldSpawn = $r->shouldSpawn($day);
        $this->assertTrue($shouldSpawn);
    }

    /** @test */
    public function it_should_spawn_monthly3()
    {
        $r = $this->getRecurringContribution();
        $r->recurring_type = 'monthly';
        $r->start_date = new Carbon('June 30, 2015');
        $r->save();
        $day = new Carbon("July 30, 2016");
        $shouldSpawn = $r->shouldSpawn($day);
        $this->assertTrue($shouldSpawn);
    }

    /** @test */
    public function it_should_spawn_monthly4()
    {
        $r = $this->getRecurringContribution();
        $r->recurring_type = 'monthly';
        $r->start_date = new Carbon('June 30, 2015');
        $r->save();
        $day = new Carbon("July 31, 2016");
        $shouldSpawn = $r->shouldSpawn($day);
        $this->assertFalse($shouldSpawn);
    }

    /** @test */
    public function it_should_spawn_yearly1()
    {
        $r = $this->getRecurringContribution();
        $r->recurring_type = 'monthly';
        $r->start_date = new Carbon('June 30, 2015');
        $r->save();
        $day = new Carbon("July 31, 2016");
        $shouldSpawn = $r->shouldSpawn($day);
        $this->assertFalse($shouldSpawn);
    }

    /** @test */
    public function it_should_spawn_yearly2()
    {
        $r = $this->getRecurringContribution();
        $r->recurring_type = 'monthly';
        $r->start_date = new Carbon('June 30, 2015');
        $r->save();
        $day = new Carbon("June 30, 2016");
        $shouldSpawn = $r->shouldSpawn($day);
        $this->assertTrue($shouldSpawn);
    }

    /** @test */
    public function it_spawns_daily()
    {
        $r = $this->getRecurringContribution();
        $r->recurring_type = 'daily';
        $r->save();
        $day = new Carbon("June 30, 2016");
        $job = new CheckRecurringContribution($r, $day);
        $job->handle();

        $this->seeInDatabase('funding_contributions', ['amount' => 100]);
    }

    /** @test */
    public function it_spawns_weekly()
    {
        $r = $this->getRecurringContribution();
        $r->recurring_type = 'weekly';
        $r->save();
        $day = new Carbon('July 9, 2015');
        $job = new CheckRecurringContribution($r, $day);
        $job->handle();

        $this->seeInDatabase('funding_contributions', ['amount' => 100]);
    }

    /** @test */
    public function it_spawns_weekly2()
    {
        $r = $this->getRecurringContribution();
        $r->recurring_type = 'weekly';
        $r->save();
        $day = new Carbon('July 19, 2015');
        $job = new CheckRecurringContribution($r, $day);
        $job->handle();

        $this->dontSeeInDatabase('funding_contributions', ['amount' => 100]);
    }

    /** @test */
    public function it_spawns_monthly()
    {
        $r = $this->getRecurringContribution();
        $r->recurring_type = 'monthly';
        $r->save();
        $day = new Carbon('September 2, 2015');
        $job = new CheckRecurringContribution($r, $day);
        $job->handle();

        $this->seeInDatabase('funding_contributions', ['amount' => 100]);
    }

    /** @test */
    public function it_spawns_monthly2()
    {
        $r = $this->getRecurringContribution();
        $r->recurring_type = 'monthly';
        $r->save();
        $day = new Carbon('Aug 6, 2015');
        $job = new CheckRecurringContribution($r, $day);
        $job->handle();

        $this->dontSeeInDatabase('funding_contributions', ['amount' => 100]);
    }

    /** @test */
    public function it_spawns_yearly()
    {
        $r = $this->getRecurringContribution();
        $r->recurring_type = 'yearly';
        $r->save();
        $day = new Carbon('July 2, 2016');
        $job = new CheckRecurringContribution($r, $day);
        $job->handle();

        $this->seeInDatabase('funding_contributions', ['amount' => 100]);
    }

    /** @test */
    public function it_spawns_yearly2()
    {
        $r = $this->getRecurringContribution();
        $r->recurring_type = 'yearly';
        $r->save();
        $day = new Carbon('July 1, 2016');
        $job = new CheckRecurringContribution($r, $day);
        $job->handle();

        $this->dontSeeInDatabase('funding_contributions', ['amount' => 100]);
    }

    /** @test */
    public function it_spawns_yearly3()
    {
        $r = $this->getRecurringContribution();
        $r->recurring_type = 'yearly';
        $r->save();
        $day = new Carbon('July 2, 2018');
        $job = new CheckRecurringContribution($r, $day);
        $job->handle();

        $this->seeInDatabase('funding_contributions', ['amount' => 100]);
    }
}