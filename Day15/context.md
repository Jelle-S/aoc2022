# Puzzle 1

Pseudo maths ahead, beware!

We know the Manhattan distance (`d`) between the Sensor (`xs, ys`) and the
Beacon (`xb, yb`) can be calculated with this formula:

```
d = | xs - xb | + | ys - yb |
```

We are looking for the intersection of a row (fixed y coordinate) with the area
described by the formula above. We know the intersection of the row with the
edges of this area will be at that same distance. Assume the coordinates of
these points to be (`xr, yr`), where `yr` is known since it is the row we're
looking for (10 in the sample, 2000000 in the puzzle).

So since we know the distance from the sensor to the beacon will be equal to the
distance from the sensor to the intersection points, we can construct this
equation.

```
| xs - xb | + | ys - yb | = | xs - xr | + | ys - yr |
```

All but one of these variables are known (`xr` is not known). So we can solve
for `xr`:

```
| xs - xb | + | ys - yb | - | ys - yr | = | xs - xr |
| xs - xb | + | ys - yb | - | ys - yr | = xs - xr or -xs + xr
```

We can immediately see we have two possible solutions, which makes sense for an
intersection between a line and a square.

So we can continue to solve for `xr` for both equations:

```
| xs - xb | + | ys - yb | - | ys - yr | = xs - xr
| xs - xb | + | ys - yb | - | ys - yr | - xs = -xr
-| xs - xb | - | ys - yb | + | ys - yr | + xs = xr
```

or

```
| xs - xb | + | ys - yb | - | ys - yr | = -xs + xr
| xs - xb | + | ys - yb | - | ys - yr | + xs = xr
```

Then we can calculate the distances of these new coordinates we calculated to
the sensor, to see if they match the distance from the sensor to the beacon. If
they don't, they're outside our search area.

For each intersection `(xr1, yr), (xr2, yr)`, the number of items in the
intersection is `| xr1 - xr2 | + 1`

## For example

### Solve for `xs=8`, `ys=7`, `xb=2`, `yb=10`, `yr=10`

```
d = | 8 - 2 | + | 7 - 10 | = 9
```

```
- |8 - 2| - |7 - 10| + |7 - 10| + 8 = 2
|8 - 2| + |7 - 10| - | 7 - 10| + 8 = 14
```

Calculated points are `2,10` and `14,10`.

Calculate the distance to the sensor:

```
d1 = | 8 - 2 | + | 7 - 10 | = 9
```
-> Matches the distance, so it's ok. This should mean the other point matches
too.

```
d2 = | 8 - 14 | + | 7 - 10 | = 9
```
-> All points between `2,10` and `14,10` (both included) are part of the
intersection, so the amount of points in the intersection is `| xr1 - xr2 | + 1`
which equals `| 2 - 14 | + 1 = 13`.

### Solve for `xs=8`, `ys=7`, `xb=2`, `yb=10`, `yr=17`

```
d = | 8 - 2 | + | 7 - 10 | = 9
```
```
- |8 - 2| - |7 - 10| + |7 - 17| + 8 = 9
|8 - 2| + |7 - 10| - | 7 - 17| + 8 = 7
```

Calculated points are `9,17` and `7,17`.

Calculate the distance to the sensor:

```
d1 = | 8 - 9 | + | 7 - 17 | = 11
```
-> Does not match the distance, so it's not ok. There is no matching
intersection. The other point shoudn't match either.

```
d2 = | 8 - 7 | + | 7 - 17 | = 11
```
-> Doesn't match either, as expected.

# Puzzle 2

It is a given in the puzzle that within those boundaries, there will only be one
possible answer. So using the method above, we can calculate the intersection
edges for every line, within those boundaries. Once we have a row with more than
one intersection (gap in the middle) or a row with one intersection whose total
number of elements is not equal to the boundry width (gap on the edge, we have
found our row (y coordinate), and can determine our x coordinate based on the
element that is missing.