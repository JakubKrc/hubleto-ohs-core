<?php

namespace Hubleto\App\External\WaiBlue\OhsCore\Services;

/**
 * Whose records the audits, documents and exams calendars show: all authors, unless "My events"
 * or a user Hubleto sends (its reminders) narrows it.
 */
class CalendarAuthor extends \Hubleto\Erp\Core
{
  // 0 means every author.
  public function getAuthorIdToShow(array $calendarFilter): int
  {
    $showsOnlyMyEvents = (int) ($calendarFilter['fOwnership'] ?? 0) == 1;
    if ($showsOnlyMyEvents) return (int) $this->authProvider()->getUserId();

    return (int) ($calendarFilter['idUser'] ?? 0);
  }
}
