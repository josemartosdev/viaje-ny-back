<?php

declare(strict_types=1);

namespace App\DataFixtures;

use App\Entity\Activity;
use App\Entity\Day;
use App\Entity\Place;
use App\Entity\Ticket;
use App\Entity\Trip;
use App\Enum\ActivityStatus;
use App\Enum\PlaceType;
use App\Enum\TicketType;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Persistence\ObjectManager;

class AppFixtures extends Fixture
{
    public function load(ObjectManager $manager): void
    {
        $trip = (new Trip())
            ->setName('Viaje NYC Agosto 2026')
            ->setCity('New York')
            ->setStartDate(new \DateTimeImmutable('2026-08-02'))
            ->setEndDate(new \DateTimeImmutable('2026-08-10'))
            ->setCurrency('USD')
            ->setNotes('Itinerario completo con visitas y guia gastronomica sin gluten.');
        $manager->persist($trip);

        $days = [
            'd2' => $this->createDay($manager, $trip, '2026-08-02', 'Llegada y primera noche en Times Square', 'Midtown Manhattan', 'Dia de llegada, hidratarse y caminar suave.', 'Vuelo, check-in y cena apta sin gluten.'),
            'd3' => $this->createDay($manager, $trip, '2026-08-03', 'Alto y Bajo Manhattan + Battery Park', 'Financial District', 'Llevar zapatillas comodas para tour largo.', 'Recorrido por zona sur de Manhattan.'),
            'd4' => $this->createDay($manager, $trip, '2026-08-04', 'DUMBO y puente de Brooklyn', 'DUMBO, Brooklyn', 'Buen dia para fotos al amanecer.', 'Visita de barrios y paseo junto al East River.'),
            'd5' => $this->createDay($manager, $trip, '2026-08-05', 'Central Park y Columbus Circle', 'Upper West Side', 'Llevar gorra y protector solar.', 'Parques, museos y bagels sin gluten.'),
            'd6' => $this->createDay($manager, $trip, '2026-08-06', 'Battery Park y ferry', 'Battery Park', 'Llevar cortavientos por el ferry.', 'Mañana de ferry y tarde libre por Downtown.'),
            'd7' => $this->createDay($manager, $trip, '2026-08-07', 'SoHo, Little Italy y Nolita', 'SoHo', 'Ideal para compras y callejear.', 'Dia de tiendas y restaurantes recomendados.'),
            'd8' => $this->createDay($manager, $trip, '2026-08-08', 'Museos y miradores', 'Midtown', 'Reservar entradas de mirador con antelacion.', 'Dia cultural y vistas de la ciudad.'),
            'd9' => $this->createDay($manager, $trip, '2026-08-09', 'Compras finales y paseo libre', 'Manhattan', 'Revisar previsiones por posibles tormentas de verano.', 'Dia flexible para repetir sitios favoritos.'),
            'd10' => $this->createDay($manager, $trip, '2026-08-10', 'Checkout y vuelo de regreso', 'Queens / Midtown', 'Salir con margen al aeropuerto.', 'Regreso con cierre de viaje.'),
        ];

        $places = [
            'hampton' => $this->createPlace($manager, 'Hampton Inn Times Square Central', PlaceType::Hotel, '220 W 41st St, New York, NY 10036', 3, '260.00', 'USD', 'https://www.hilton.com', null, 'Hotel base del viaje.'),
            'jfk' => $this->createPlace($manager, 'JFK Terminal 8', PlaceType::Transport, 'John F. Kennedy International Airport, Queens', 2, null, 'USD', null, null, 'Llegadas y salidas internacionales.'),
            'battery' => $this->createPlace($manager, 'Battery Park', PlaceType::Park, 'Battery Park, New York, NY', 1, null, 'USD', null, null, 'Punto clave para ferry y vistas de la bahia.'),
            'dumbo' => $this->createPlace($manager, 'DUMBO Waterfront', PlaceType::Monument, 'DUMBO, Brooklyn, NY', 1, null, 'USD', null, null, 'Zona clasica para vistas del puente y skyline.'),
            'centralPark' => $this->createPlace($manager, 'Central Park', PlaceType::Park, 'Central Park, New York, NY', 1, null, 'USD', null, null, 'Paseo principal del dia 5.'),
            'met' => $this->createPlace($manager, 'The Metropolitan Museum of Art', PlaceType::Museum, '1000 5th Ave, New York, NY 10028', 3, '30.00', 'USD', 'https://www.metmuseum.org', null, 'Museo recomendado en dia cultural.'),
            'topOfRock' => $this->createPlace($manager, 'Top of the Rock', PlaceType::Monument, '30 Rockefeller Plaza, New York, NY 10112', 3, '44.00', 'USD', 'https://www.rockefellercenter.com', null, 'Mirador con vistas de Manhattan.'),
            'friedmans' => $this->createPlace($manager, 'Friedmans', PlaceType::Restaurant, 'Times Square, New York, NY', 2, '28.00', 'USD', null, null, 'Primera cena recomendada, menu adaptado sin gluten.'),
            'losTacos' => $this->createPlace($manager, 'Los Tacos No. 1', PlaceType::Restaurant, 'Times Square, New York, NY', 1, '15.00', 'USD', null, null, 'Tacos iconicos con tortilla de maiz.'),
            'bareburger' => $this->createPlace($manager, 'Bareburger', PlaceType::Restaurant, 'Midtown, New York, NY', 2, '22.00', 'USD', null, null, 'Hamburguesa con opcion de pan sin gluten.'),
            'littleBeet' => $this->createPlace($manager, 'The Little Beet', PlaceType::Restaurant, 'Financial District, New York, NY', 2, '24.00', 'USD', 'https://www.thelittlebeet.com', null, 'Casi todo sin gluten.'),
            'lukes' => $this->createPlace($manager, 'Luke\'s Lobster', PlaceType::Restaurant, '26 S William St, New York, NY 10004', 2, '29.00', 'USD', 'https://www.lukeslobster.com', null, 'Lobster roll con opcion adaptada sin gluten.'),
            'timeout' => $this->createPlace($manager, 'Time Out Market New York', PlaceType::Restaurant, '55 Water St, Brooklyn, NY 11201', 2, '23.00', 'USD', 'https://www.timeoutmarket.com', null, 'Food hall con varias opciones GF.'),
            'westville' => $this->createPlace($manager, 'Westville DUMBO', PlaceType::Restaurant, '81 Washington St, Brooklyn, NY 11201', 2, '26.00', 'USD', null, null, 'Producto fresco y opciones sin gluten.'),
            'modernBread' => $this->createPlace($manager, 'Modern Bread & Bagel', PlaceType::Restaurant, '472 Columbus Ave, New York, NY 10024', 2, '20.00', 'USD', 'https://www.modernbreadandbagel.com', null, 'Imprescindible del viaje, 100% gluten free.'),
            'tapNyc' => $this->createPlace($manager, 'Tap NYC', PlaceType::Restaurant, '267 Columbus Ave, New York, NY 10023', 2, '19.00', 'USD', 'https://www.tap-nyc.com', null, 'Cocina brasilena casi totalmente gluten free.'),
            'dig' => $this->createPlace($manager, 'DIG', PlaceType::Restaurant, 'Broadway, New York, NY', 2, '18.00', 'USD', 'https://www.diginn.com', null, 'Bowls saludables para comer rapido.'),
            'bills' => $this->createPlace($manager, 'Bill\'s Bar & Burger', PlaceType::Restaurant, '85 West St, New York, NY 10006', 2, '23.00', 'USD', null, null, 'Burger americana con pan sin gluten.'),
            'senza' => $this->createPlace($manager, 'Senza Gluten by Jemiko', PlaceType::Restaurant, '206 Sullivan St, New York, NY 10012', 3, '42.00', 'USD', 'https://www.senzaglutennyc.com', null, 'Experiencia sin gluten imprescindible.'),
            'rubirosa' => $this->createPlace($manager, 'Rubirosa', PlaceType::Restaurant, '235 Mulberry St, New York, NY 10012', 3, '36.00', 'USD', 'https://www.rubirosanyc.com', null, 'Pizza sin gluten, mejor con reserva.'),
            'thaiDiner' => $this->createPlace($manager, 'Thai Diner', PlaceType::Restaurant, '186 Mott St, New York, NY 10012', 3, '34.00', 'USD', 'https://www.thaidinernyc.com', null, 'Local muy popular en Nolita.'),
        ];

        $arrivalFlight = $this->createActivity(
            $manager,
            $days['d2'],
            $places['jfk'],
            'Vuelo Madrid - Nueva York BA4267',
            'flight',
            '12:30',
            '15:00',
            ActivityStatus::Reserved,
            '0.00',
            'EUR',
            '7YZ5JJ',
            'Operado por Iberia, llegada a JFK T8.'
        );
        $this->createActivity($manager, $days['d2'], $places['hampton'], 'Check-in hotel y descanso', 'hotel', '17:30', null, ActivityStatus::Planned, null, 'USD', null, 'Dejar maletas y paseo suave.');
        $d2Dinner = $this->createActivity($manager, $days['d2'], $places['friedmans'], 'Cena inicial en Times Square', 'food', '20:00', null, ActivityStatus::Flexible, '30.00', 'USD', null, 'Alternativas: Los Tacos No. 1 o Bareburger.');

        $d3Tour = $this->createActivity($manager, $days['d3'], null, 'Excursion Alto y Bajo Manhattan', 'tour', '09:00', '13:00', ActivityStatus::Reserved, '48.00', 'EUR', null, 'Recorrido principal por Downtown.');
        $this->createActivity($manager, $days['d3'], $places['battery'], 'Paseo por Battery Park', 'walk', '14:00', '15:30', ActivityStatus::Planned, null, 'USD', null, null);
        $this->createActivity($manager, $days['d3'], $places['littleBeet'], 'Comida en The Little Beet', 'food', '16:00', null, ActivityStatus::Planned, '26.00', 'USD', null, 'Recomendacion principal del dia 3.');
        $this->createActivity($manager, $days['d3'], $places['lukes'], 'Cena en Luke\'s Lobster', 'food', '20:00', null, ActivityStatus::Flexible, '32.00', 'USD', null, 'Probar lobster roll adaptado sin gluten.');

        $this->createActivity($manager, $days['d4'], $places['dumbo'], 'Paseo y fotos en DUMBO', 'walk', '09:30', '11:30', ActivityStatus::Planned, null, 'USD', null, null);
        $this->createActivity($manager, $days['d4'], $places['timeout'], 'Comida en Time Out Market', 'food', '13:00', null, ActivityStatus::Flexible, '25.00', 'USD', null, 'Ideal para elegir varias opciones GF.');
        $this->createActivity($manager, $days['d4'], $places['westville'], 'Cena en Westville DUMBO', 'food', '20:30', null, ActivityStatus::Planned, '28.00', 'USD', null, null);

        $this->createActivity($manager, $days['d5'], $places['centralPark'], 'Paseo por Central Park', 'walk', '09:00', '11:00', ActivityStatus::Planned, null, 'USD', null, null);
        $this->createActivity($manager, $days['d5'], $places['modernBread'], 'Brunch en Modern Bread & Bagel', 'food', '11:30', null, ActivityStatus::Planned, '22.00', 'USD', null, 'Top 1 del viaje sin gluten.');
        $this->createActivity($manager, $days['d5'], $places['tapNyc'], 'Merienda en Tap NYC', 'food', '17:30', null, ActivityStatus::Flexible, '18.00', 'USD', null, null);
        $this->createActivity($manager, $days['d5'], $places['dig'], 'Cena ligera en DIG', 'food', '20:00', null, ActivityStatus::Flexible, '19.00', 'USD', null, null);

        $ferryActivity = $this->createActivity($manager, $days['d6'], $places['battery'], 'Ferry y paseo por zona sur', 'transport', '09:30', '12:00', ActivityStatus::Reserved, '29.00', 'USD', 'FRY-0608', 'Actividad de manana con reserva.');
        $this->createActivity($manager, $days['d6'], $places['littleBeet'], 'Comida en The Little Beet (repetir)', 'food', '13:00', null, ActivityStatus::Planned, '24.00', 'USD', null, null);
        $this->createActivity($manager, $days['d6'], $places['bills'], 'Cena en Bill\'s Bar & Burger', 'food', '20:15', null, ActivityStatus::Planned, '27.00', 'USD', null, null);

        $this->createActivity($manager, $days['d7'], null, 'Ruta de tiendas por SoHo', 'shopping', '10:00', '13:00', ActivityStatus::Planned, null, 'USD', null, null);
        $senzaActivity = $this->createActivity($manager, $days['d7'], $places['senza'], 'Cena en Senza Gluten by Jemiko', 'food', '19:30', null, ActivityStatus::Reserved, '52.00', 'USD', 'SNZ-0708', 'Reserva recomendada.');
        $this->createActivity($manager, $days['d7'], $places['rubirosa'], 'Alternativa cena en Rubirosa', 'food', '20:30', null, ActivityStatus::Flexible, '36.00', 'USD', null, 'Pizza sin gluten.');
        $this->createActivity($manager, $days['d7'], $places['thaiDiner'], 'Late dinner en Thai Diner', 'food', '22:00', null, ActivityStatus::Flexible, '34.00', 'USD', null, null);

        $metActivity = $this->createActivity($manager, $days['d8'], $places['met'], 'Visita al MET', 'museum', '10:00', '13:00', ActivityStatus::Reserved, '30.00', 'USD', null, 'Museo principal del dia 8.');
        $topActivity = $this->createActivity($manager, $days['d8'], $places['topOfRock'], 'Subida al Top of the Rock', 'viewpoint', '19:00', '20:00', ActivityStatus::Reserved, '44.00', 'USD', 'TOR-0808', 'Intentar subir al atardecer.');

        $this->createActivity($manager, $days['d9'], null, 'Dia libre para repetir favoritos', 'flex', '10:00', null, ActivityStatus::Flexible, null, 'USD', null, 'Ideal para compras y ultimas fotos.');
        $this->createActivity($manager, $days['d9'], $places['modernBread'], 'Desayuno despedida', 'food', '09:00', null, ActivityStatus::Flexible, '20.00', 'USD', null, null);
        $this->createActivity($manager, $days['d9'], $places['losTacos'], 'Cena informal Los Tacos No. 1', 'food', '21:00', null, ActivityStatus::Flexible, '17.00', 'USD', null, 'Top 4 gastronomico del viaje.');

        $returnActivity = $this->createActivity($manager, $days['d10'], $places['jfk'], 'Vuelo Nueva York - Madrid', 'flight', '18:00', null, ActivityStatus::Reserved, '0.00', 'EUR', '7YZ5JJ', 'Llegar al aeropuerto con 3 horas de margen.');

        $this->createTicket($manager, $days['d2'], $arrivalFlight, TicketType::Boarding, 'Boarding pass BA4267', 'British Airways', '7YZ5JJ', 'Ana Corbalan', null, 'T4S', null, 'EUR', null, 'Conservar para embarque.');
        $this->createTicket($manager, $days['d3'], $d3Tour, TicketType::Reservation, 'Reserva tour Alto y Bajo Manhattan', 'Tour operador local', null, null, null, null, '48.00', 'EUR', null, null);
        $this->createTicket($manager, $days['d6'], $ferryActivity, TicketType::Pass, 'Pase ferry Downtown', 'City Cruises', 'FRY-0608', null, null, 'Pier 11', '29.00', 'USD', null, null);
        $this->createTicket($manager, $days['d7'], $senzaActivity, TicketType::Reservation, 'Reserva Senza Gluten by Jemiko', 'Senza Gluten', 'SNZ-0708', 'Ana Corbalan', null, null, null, 'USD', null, 'Confirmar llegada 10 min antes.');
        $this->createTicket($manager, $days['d8'], $metActivity, TicketType::Entry, 'Entrada MET', 'The Met', 'MET-0808', null, null, null, '30.00', 'USD', null, null);
        $this->createTicket($manager, $days['d8'], $topActivity, TicketType::Entry, 'Entrada Top of the Rock', 'Rockefeller Center', 'TOR-0808', null, null, null, '44.00', 'USD', null, null);
        $this->createTicket($manager, $days['d10'], $returnActivity, TicketType::Boarding, 'Boarding pass regreso BA4268', 'British Airways', '7YZ5JJ', 'Ana Corbalan', '28A', 'T8', null, 'EUR', null, null);

        $manager->flush();
    }

    private function createDay(
        ObjectManager $manager,
        Trip $trip,
        string $date,
        string $title,
        ?string $district,
        ?string $weatherTip,
        ?string $notes
    ): Day {
        $day = (new Day())
            ->setTrip($trip)
            ->setDate(new \DateTimeImmutable($date))
            ->setTitle($title)
            ->setDistrict($district)
            ->setWeatherTip($weatherTip)
            ->setNotes($notes);
        $manager->persist($day);

        return $day;
    }

    private function createPlace(
        ObjectManager $manager,
        string $name,
        PlaceType $type,
        ?string $address,
        ?int $priceLevel,
        ?string $averagePrice,
        ?string $currency,
        ?string $website,
        ?string $phone,
        ?string $notes
    ): Place {
        $place = (new Place())
            ->setName($name)
            ->setType($type)
            ->setAddress($address)
            ->setPriceLevel($priceLevel)
            ->setAveragePrice($averagePrice)
            ->setCurrency($currency)
            ->setWebsite($website)
            ->setPhone($phone)
            ->setNotes($notes);
        $manager->persist($place);

        return $place;
    }

    private function createActivity(
        ObjectManager $manager,
        Day $day,
        ?Place $place,
        string $title,
        string $category,
        ?string $startTime,
        ?string $endTime,
        ActivityStatus $status,
        ?string $price,
        ?string $currency,
        ?string $bookingCode,
        ?string $notes
    ): Activity {
        $activity = (new Activity())
            ->setDay($day)
            ->setPlace($place)
            ->setTitle($title)
            ->setCategory($category)
            ->setStartTime($this->time($startTime))
            ->setEndTime($this->time($endTime))
            ->setStatus($status)
            ->setPrice($price)
            ->setCurrency($currency)
            ->setBookingCode($bookingCode)
            ->setNotes($notes);
        $manager->persist($activity);

        return $activity;
    }

    private function createTicket(
        ObjectManager $manager,
        Day $day,
        ?Activity $activity,
        TicketType $type,
        string $title,
        ?string $provider,
        ?string $code,
        ?string $holder,
        ?string $seat,
        ?string $gate,
        ?string $price,
        ?string $currency,
        ?string $documentUrl,
        ?string $notes
    ): Ticket {
        $ticket = (new Ticket())
            ->setDay($day)
            ->setActivity($activity)
            ->setType($type)
            ->setTitle($title)
            ->setProvider($provider)
            ->setCode($code)
            ->setHolder($holder)
            ->setSeat($seat)
            ->setGate($gate)
            ->setPrice($price)
            ->setCurrency($currency)
            ->setDocumentUrl($documentUrl)
            ->setNotes($notes);
        $manager->persist($ticket);

        return $ticket;
    }

    private function time(?string $value): ?\DateTimeImmutable
    {
        if ($value === null || $value == '') {
            return null;
        }

        return new \DateTimeImmutable(sprintf('1970-01-01 %s:00', $value));
    }
}
