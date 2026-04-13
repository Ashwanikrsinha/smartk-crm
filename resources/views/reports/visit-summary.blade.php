<table class="table table-sm text-center" style="min-width: 30rem;">
    <thead>
       <tr class="text-secondary text-uppercase">
          <th class="fw-normal ps-3 text-start">Executive</th>
          <th class="fw-normal">Today</th>
          <th class="fw-normal">Yesterday</th>
          <th class="fw-normal">Last 7 Days</th>
          <th class="fw-normal">This Month</th>
          <th class="fw-normal pe-3">Last Month</th>
       </tr>
    </thead> 
 
       @php
          $today_visits_total = 0;
          $yesterday_visits_total = 0;
          $last_seven_day_visits_total = 0;
          $current_month_visits_total = 0;
          $last_month_visits_total = 0;
       @endphp
 
       @foreach ($users as $user)
       
       @php
          $today_visits_total += $user->today_visits_count;
          $yesterday_visits_total += $user->yesterday_visits_count;
          $last_seven_day_visits_total += $user->last_seven_days_visits_count;
          $current_month_visits_total += $user->current_month_visits_count;
          $last_month_visits_total += $user->last_month_visits_count;
       @endphp
       <tbody>
          <tr>
             <td class="ps-3 text-start">{{ $user->username }}</td>
             <td>{{ $user->today_visits_count }}</td>
             <td>{{ $user->yesterday_visits_count }}</td>
             <td>{{ $user->last_seven_day_visits_count }}</td>
             <td>{{ $user->current_month_visits_count }}</td>
             <td class="pe-3">{{ $user->last_month_visits_count }}</td>
             @if ($loop->last)
                <tr class="fw-bold">
                   <td class="text-start ps-3">Total</td>
                   <td>{{ $today_visits_total }}</td>
                   <td>{{ $yesterday_visits_total }}</td>
                   <td>{{ $last_seven_day_visits_total }}</td>
                   <td>{{ $current_month_visits_total }}</td>
                   <td>{{ $last_month_visits_total }}</td>
                </tr>
             @endif
          </tr>
          @endforeach
    </tbody>
    </table>