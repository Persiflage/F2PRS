# F2PRS

**Note:** The [v3](https://github.com/pacocoursey/f2prs/tree/v3.0.0-alpha) branch is under heavy development - the stable release branch is [master](https://github.com/pacocoursey/f2prs/tree/master).

F2PRS is a hiscores for pure free-to-play RuneScape players.

## Usage

A package manager like `npm` or `yarn` is required.

First, clone the repo:

```bash
$ git clone https://github.com/pacocoursey/f2prs.git
$ cd f2prs
```

Then, install packages and start the development server:

```bash
$ yarn install  # or npm install
$ yarn dev      # or npm run dev
```

## Implementation

The front-end is written in [React](https://reactjs.org/), using the [Next.js](https://nextjs.org) framework, and styled with [styled-jsx](https://github.com/zeit/styled-jsx). The back-end API is written using [Next.js API routes](https://nextjs.org/blog/next-9#api-routes). The back-end uses a MySQL database.

## Database Design

The database uses the following tables:

| users       | banlist         | recent        | stats      | day/week/month |
| ----------- | --------------- | ------------- | -----------| -------------- |
| `id`        | `id` (fk)       | `id` (fk)     | `id` (fk)  | `id` (fk)      |
| `rsn`       |                 | `time`        | `time`     | `start_time`   |
|             |                 | `achievement` | `type`     | `end_time`     |
|             |                 | `skill`       | `...xp`    | `...xp`        |
|             |                 |               | `...level` | `...level`     |
|             |                 |               | `...rank`  | `...rank`      |


## Use Cases

### Tracking Page Visit

- User queries tracking page for `persiflage` with time frame `week`.
- Page requests most recent weekly data for `persiflage` from the API.
- API queries the `week` table of the database, searching for entries that are within the last 7 days.
- API returns this data to the page, and the page displays the gain table.

### Tracking Page Update

- User clicks the update button on the tracking page for `persiflage` with time frame `week`.
- Page sends a request to the API to update the player.
- API fetches the most recent stats from the RuneScape Hiscores API.
- API retrieves the oldest valid entry in the `stats` table for the past day/week/month (using `start_time`), and calculates the difference for each time category.
- API stores the respective difference (gain) in the day/week/month tables. The API finally returns the newly updated `week` gains to the page, and the page displays the gain table.

### Current Top Weekly

- User queries current top page with time frame `week`.
- Page send a request to the API for the current top weekly gains (limit X based on page number).
- API retrieves entries in the past 7 days (based on `start_time`), sorted by descending gain, and unique per user id.
- API returns this list to the page, and the page displays the current top table.

### Week Records

- User queries records page with time frame `week`.
- Page sends a request to the API for the weekly records.
- API retrieves entries with the `start_time` and `end_time` within 7 days of each other, sorted by descending gain.
- API returns this list to the page, and the page dislays the current top table.
