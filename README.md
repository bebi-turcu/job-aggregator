<h1 align="center">Job Aggregator</h1>

<p align="center">
  <a href="https://packagist.org/packages/laravel-zero/framework"><img src="https://img.shields.io/packagist/l/laravel-zero/framework.svg" alt="License"></a>
</p>

<h4 align="center">A **CLI application** for aggregating jobs from data feeds</h4>

Built using the [Laravel Zero](https://laravel-zero.com/) micro-framework, this basic project provides a console command
to be run periodically as a cron job. It works by connecting to multiple pre-defined feed endpoints, parsing data read
from them and saving jobs to a local database. Includes unit tests.

Pre-requisites: PHP 8.0, MySQL 5.7, Composer 2.4 and a webserver like Apache.

------

## Task requirements

This project was built to satisfy the following requirements:

"We have a database which contains a list of Job advertisements, used in the backend of a website for displaying job listings.

- The jobs on the website come from a
number of different partners job feeds. Each day, each partner provides us with a CSV file containing their jobs.
- Each feed contains a number of columns, but we are only concerned with:
  `job_title, job_description, company, posted_date, cpc_value`.
- Each partner’s job feed must contain at least these five columns but other columns may be present, and vary from feed to
  feed. If these required columns do not exist, the feed must be rejected.
- Each feed can contain any number of jobs, and this varies from feed to feed.
- Each feed is processed once per day.
- To determine which job belongs to which partner we need to store a `partner_id` value against the job, linking to another
  database that stores details about the partner.
- It is very common that two or more partners will list the same job, so we need to determine which job has the highest
  `cpc_value` and only store this job to be used on the website. The other job can be discarded.
- When determining if two or more partners list the same job in their feed, assume that if the `job_title`, `job_description`
  and `company` are the same values between the two jobs, then the job is considered to be a duplicate.
- When the job is stored in our database, we assign a job `id` value to uniquely identify the job in our system.
- It’s also possible that a partner has stopped listing a job, so it is important that these old jobs are removed from the DB.
- It's important that live jobs keep the same job `id` if they already exist within the DB, and if not should be assigned an
  automatic incremental `id`.
- Existing jobs should have their details updated in the DB. To determine when a job was last updated, we store a `last_updated`
  value against the job which contains the last time the job was updated."

## Installation

Deploy the project files in a folder of your host by cloning this repository or extracting from a downloaded archive. Then: 

- Point your webserver to folder `/public` and make sure you can access this in a browser: `http://localhost/feed1.txt`
- Run `composer install` in your console
- Create a MySQL database named `job_aggregator` using collation `utf8_general_ci` and import the file `/db.sql` to populate
  it with tables
- Adjust `/config/database.php` to match your DB credentials

## Usage

Run this command in the console: `php aggregator feeds:process`.

A message will show imported jobs on the screen and table `jobs` will be updated.<br>
Data feeds are simulated using txt files in our *localhost*'s `/public` directory. Edit these files to simulate various
data content coming from feeds, run the console command above and then see how records change in table `jobs`.<br>
Feed *URLs* can be adjusted in table `feeds`.

Unit tests can be run using the console command: `vendor\bin\phpunit`

## License

Job Aggregator is an open-source software licensed under the MIT license.
