<?php

namespace Database\Seeders;

use App\Models\Service;
use App\Models\ServiceCategory;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Str;

/**
 * Safe to run more than once.
 * - Categories are matched by name, services by slug (the slug is built from the name).
 * - Existing services get their category and description updated.
 * - Images and the Active switch of existing services are never touched.
 * - New services are created as active, with no image.
 */
class ServiceCatalogSeeder extends Seeder
{
    public function run(): void
    {
        $categorySlugColumn   = Schema::hasColumn('service_categories', 'slug');
        $categoryActiveColumn = Schema::hasColumn('service_categories', 'is_active');

        foreach ($this->catalog() as $index => $group) {
            $category = ServiceCategory::where('name', $group['name'])->first() ?? new ServiceCategory();
            $isNewCategory = ! $category->exists;

            $category->forceFill([
                'name'       => $group['name'],
                'sort_order' => $index + 1,
            ]);

            if ($isNewCategory && $categorySlugColumn) {
                $category->slug = Str::slug($group['name']);
            }
            if ($isNewCategory && $categoryActiveColumn) {
                $category->is_active = true;
            }

            $category->save();

            foreach ($group['services'] as $item) {
                $slug = Str::slug($item['name']);

                $service = Service::where('slug', $slug)->first() ?? new Service();
                $isNewService = ! $service->exists;

                $service->forceFill([
                    'service_category_id' => $category->id,
                    'name'                => $item['name'],
                    'slug'                => $slug,
                    'description'         => $item['description'],
                ]);

                if ($isNewService) {
                    $service->is_active = true;
                }

                $service->save();
            }
        }
    }

    private function catalog(): array
    {
        return [
            [
                'name' => 'Residential Electrical',
                'services' => [
                    [
                        'name' => 'House wiring and rewiring',
                        'description' => "Whether you are building a new home or living in an older one, the wiring behind your walls decides how safe and reliable your electricity is. We install complete wiring for new houses, from the main supply point through every room, and we rewire older homes where cables are worn, overloaded or no longer safe. We use the right cable sizes for each circuit and protect them properly, so your lights, sockets and appliances get steady power.\n\nWarning signs that a house needs rewiring include breakers that trip often, sockets that feel warm, burning smells, flickering lights and cables that have cracked or turned brittle. We can rewire a whole house or only the affected rooms, and we will explain what needs doing before any work starts.",
                    ],
                    [
                        'name' => 'Sockets, switches and lighting',
                        'description' => "Good sockets and switches make a home easier and safer to live in. We install new sockets and switches, replace loose, cracked or burnt ones, and add extra points where you keep running extension cables. We also fit light fittings of all kinds, including ceiling lights, wall lights, bulkheads and spotlights.\n\nIf a room never seems to have enough sockets, or a switch sparks or crackles when you use it, it is worth having it checked. We will suggest sensible positions for new points so the wiring is neat and the finished room looks tidy.",
                    ],
                    [
                        'name' => 'Fan and water heater installation',
                        'description' => "Fans, instant showers and water heaters draw a lot of power, and fitting them wrongly is one of the most common causes of electrical faults at home. We mount ceiling and wall fans securely and wire them with the right switches. For showers and water heaters we use the correct cable size and a suitable breaker, and we connect proper earthing so the metal parts do not become dangerous.\n\nBring us the appliance you have bought, or ask us what to look out for before you buy. We will check that your board and wiring can carry the extra load before we install, and we will tell you if anything needs upgrading first.",
                    ],
                    [
                        'name' => 'Distribution board (DB) upgrades',
                        'description' => "The distribution board is the box that splits power to every circuit in your home and protects you when something goes wrong. Old fuse boxes and crowded boards can overheat, trip without reason or fail to protect you at all. We replace them with modern distribution boards fitted with proper breakers, and we add earth leakage protection where it is needed.\n\nDuring the upgrade we label each circuit clearly, so you know which breaker controls which part of the house. This makes future repairs faster and safer, and it is a worthwhile step before you add solar, a new water heater or any other heavy load.",
                    ],
                ],
            ],
            [
                'name' => 'Commercial and Industrial Electrical',
                'services' => [
                    [
                        'name' => 'Shop, office and building wiring',
                        'description' => "A business needs electrical work that is planned properly from the start, because outages and faults cost you customers and time. We wire shops, offices, schools, clinics, hotels and apartment blocks, from the main supply and distribution boards through to every socket, switch and light point. We plan the circuits around how the space will be used, with enough sockets for equipment and separate circuits for heavy loads.\n\nWe work in a tidy, organised way and can coordinate with builders and other tradespeople on your site, so electrical work fits in with the rest of the construction. Every circuit is tested before handover.",
                    ],
                    [
                        'name' => 'Three-phase installations',
                        'description' => "Many workshops, hotels, farms and larger businesses need three-phase power to run heavy machines smoothly. We install and connect three-phase supplies, distribution boards and the wiring to your equipment, and we balance the load across the phases so nothing is overstrained.\n\nIf you are planning to bring in a welding machine, large motor, commercial kitchen equipment or a big air conditioning system, talk to us early. We will advise on the supply and wiring the equipment needs, so you avoid trips, overheating and damage to expensive machines.",
                    ],
                    [
                        'name' => 'Motor and control panel wiring',
                        'description' => "Pumps, mills, compressors and production machines all rely on correct motor wiring and reliable controls. We wire and connect motors, starters and control panels, and install the isolators and protection devices that keep both the machine and the operator safe.\n\nWe can also build or repair control panels, replace worn contactors and relays, and trace faults when a machine will not start or keeps stopping. Good control wiring means less downtime, and when something does fail, a neat and labelled panel makes it quick to repair.",
                    ],
                    [
                        'name' => 'Cable trunking and conduit',
                        'description' => "Cables that run loose along walls and ceilings can be damaged, cause accidents and look untidy. We install trunking, conduit and cable trays that protect the wiring and keep it organised. This suits offices, shops, workshops and any building where cables need to be added or changed later.\n\nSurface-mounted trunking is a good solution when you cannot chisel into walls, for example in a rented or finished building. We install it in straight, neat runs and finish it so it blends into the room.",
                    ],
                ],
            ],
            [
                'name' => 'Solar and Backup Power',
                'services' => [
                    [
                        'name' => 'Solar PV design and installation',
                        'description' => "Solar panels can cut your electricity bill and keep you powered through outages. We start by looking at how much power you use, what you want to run and where panels can be placed, then we design a system that fits your needs and budget. Installation covers the panels, mounting frames, cabling, protection devices and connection to your existing wiring.\n\nYou will get a clear explanation of what the system can and cannot run, so you know what to expect on a cloudy day or at night. We can design the system so it can be extended later if your needs grow.",
                    ],
                    [
                        'name' => 'Inverter and battery backup',
                        'description' => "An inverter and battery system keeps the essentials running when the grid goes off. We size the system around what you want to keep powered, such as lights, TV, fridge, Wi-Fi router and phone chargers, and we install the inverter, batteries and wiring neatly and safely.\n\nWe can connect the system to a separate set of circuits, so only the important loads run on battery and the batteries last longer. We will also show you how to read the inverter and how to look after the batteries so you get the longest life from them.",
                    ],
                    [
                        'name' => 'Generator and changeover installation',
                        'description' => "A generator is only useful if it is connected safely. We install a changeover switch, either manual or automatic, that moves your supply between the mains and the generator without ever connecting both at once. This protects your appliances and, more importantly, the people working on the power lines outside.\n\nWe also advise on where to place the generator for ventilation and noise, and on the cable and breaker sizes it needs. This is a good fit for homes, shops, clinics and offices that cannot afford to lose power.",
                    ],
                    [
                        'name' => 'Solar water heating',
                        'description' => "Heating water is one of the biggest electricity costs in a home or business. A solar water heater uses the sun to heat your water, so you get hot showers and kitchen water with very little running cost. We supply and install the collector, storage tank, piping and the electrical backup element for cloudy days.\n\nThis works well for homes, guest houses, schools, clinics and hotels where hot water is used every day. We will help you choose the right tank size for the number of people using it.",
                    ],
                ],
            ],
            [
                'name' => 'Lighting',
                'services' => [
                    [
                        'name' => 'Indoor lighting design and installation',
                        'description' => "Good lighting changes how a room looks and feels, and affects how well you can work, read and relax. We plan lighting for homes, shops, restaurants and offices, choosing the number, position and type of fittings to suit the space. This can include ceiling lights, spotlights, pendant lights, wall lights and feature lighting.\n\nWe also install dimmers and multiple switch points where they help. Tell us what the room is used for and we will suggest a layout that is bright where you need it and comfortable everywhere else.",
                    ],
                    [
                        'name' => 'Outdoor and compound lighting',
                        'description' => "Well-placed outdoor lights make your property safer and more welcoming after dark. We install lighting for gates, walkways, gardens, parking areas and building exteriors, using fittings and cables that are made to handle rain, dust and heat.\n\nWe can set the lights on a switch, a timer or a light sensor, so they come on automatically at dusk. Proper outdoor wiring is waterproofed and protected, which prevents the shorts and trips that often follow when outdoor lights are installed carelessly.",
                    ],
                    [
                        'name' => 'Security and floodlighting',
                        'description' => "Strong, well-aimed light is one of the simplest ways to discourage intruders. We install floodlights and motion-sensor lights at entrances, along perimeter walls and around dark corners where people could hide. Motion-sensor lights switch on only when someone approaches, which saves power and draws attention to movement.\n\nWe position each light to cover the area properly without shining into neighbours' windows. Floodlighting also works well alongside cameras, because it gives them a clear picture at night.",
                    ],
                    [
                        'name' => 'LED upgrades',
                        'description' => "LED lights use far less electricity than old bulbs and tubes and last many times longer, which lowers your power bill and the cost of replacing bulbs. We replace old fittings in homes, shops, offices, warehouses and schools with LED panels, tubes, bulbs and downlights.\n\nWe help you choose the right brightness and colour of light for each space, because the wrong choice can make a room look harsh or dim. For larger buildings, an LED upgrade often pays for itself through the savings on electricity.",
                    ],
                ],
            ],
            [
                'name' => 'Security Systems and Electric Fencing',
                'services' => [
                    [
                        'name' => 'Electric fence installation',
                        'description' => "An electric fence along your wall or boundary is a strong deterrent against intruders. We install the fence wires, insulators, energiser, earthing and warning signs, and we set it up so it gives a short, safe pulse that discourages climbing without causing harm. We also connect an alarm or siren to the fence, which sounds if someone touches or cuts it.\n\nGood earthing and correct installation are what make an electric fence both effective and safe. We will show you how to switch it on and off and how to check that it is working, and we can service it later if the readings drop.",
                    ],
                    [
                        'name' => 'CCTV installation',
                        'description' => "Cameras let you see what is happening at your home or business, and the footage can help if something goes wrong. We plan where cameras should go to cover entrances, parking, stock rooms and counters, then install the cameras, recorder, cabling and power supply.\n\nYou can usually set up viewing on your phone so you can check in from anywhere. We choose cameras that suit the lighting, such as night-vision models for dark areas, and we explain how to find and save recordings when you need them.",
                    ],
                    [
                        'name' => 'Alarm and access control',
                        'description' => "An alarm system alerts you when someone enters where they should not, and access control decides who can go in and when. We install burglar alarms with door and motion sensors and sirens, and access systems using keypads, cards or fingerprints for offices, shops, schools and apartment blocks.\n\nAccess control also saves the hassle of managing keys, because you can add or remove people without changing locks. We will set the system up around how your building is used and show you how to manage it.",
                    ],
                    [
                        'name' => 'Gate automation and intercom',
                        'description' => "An automatic gate saves you from getting out of the car in the rain or at night, and it improves your security. We install motors for sliding and swing gates, with remote controls, safety sensors and a manual release for use during power cuts.\n\nWe also fit intercoms and door-release systems, so you can speak to a visitor and let them in without going to the gate. This suits homes, apartment blocks and businesses where visitors and deliveries arrive during the day.",
                    ],
                ],
            ],
            [
                'name' => 'Safety, Earthing and Fault Finding',
                'services' => [
                    [
                        'name' => 'Fault finding',
                        'description' => "When breakers keep tripping, sockets go dead or lights flicker, the cause is not always obvious. We use testing instruments to trace the fault step by step, rather than guessing and replacing parts at random. Common causes include loose connections, damaged cables, overloaded circuits, faulty appliances and water getting into wiring.\n\nOnce we find the cause, we explain it in plain words and fix it properly. This saves you money compared to repeated call-outs for the same problem, and it removes the risk of fire or shock that unresolved faults can carry.",
                    ],
                    [
                        'name' => 'Earthing and surge protection',
                        'description' => "Earthing gives dangerous electricity a safe path into the ground instead of through a person. We install earth rods, conductors and connections to the standard that protects your family, and we test the earth resistance to confirm that it works. Good earthing is especially important for showers, water heaters, fridges and any metal-bodied appliance.\n\nSurge protectors guard TVs, computers, fridges and routers against sudden voltage spikes from the grid or from lightning. We fit them at the distribution board and at sensitive equipment, so one bad storm does not destroy expensive appliances.",
                    ],
                    [
                        'name' => 'Lightning protection',
                        'description' => "Lightning strikes can start fires, damage electrical systems and endanger people inside a building. A lightning protection system gives the strike a safe, controlled path to the ground. We install lightning rods or conductors at the highest points of the building, with down conductors and a proper earth connection.\n\nThis is a sensible choice for tall buildings, schools, churches, clinics, hotels, farms and any building in an exposed or high-risk area. We can also check that your existing system is intact and properly connected.",
                    ],
                    [
                        'name' => 'Electrical safety inspection and testing',
                        'description' => "An inspection tells you whether your wiring is safe before something goes wrong. We check the distribution board, circuits, sockets, switches, earthing and protection devices, and we test them with proper instruments. You get a clear explanation of what is in good order and what needs to be fixed, with the most urgent items identified first.\n\nThis is useful when buying or renting a property, after a fire or flood, before a building is occupied, or when you simply want peace of mind about an older installation.",
                    ],
                ],
            ],
            [
                'name' => 'Maintenance and Repairs',
                'services' => [
                    [
                        'name' => 'Emergency electrical repairs',
                        'description' => "Sparks, burning smells, a sudden loss of power or a shock from an appliance are problems that should not wait. If you have an urgent electrical problem, call us and we will do our best to attend and make the situation safe. While you wait, switch off the affected circuit or the main switch if it is safe to do so, and keep away from damaged wiring.\n\nOnce the danger is dealt with, we find the cause and repair it properly, so you are not left with a temporary fix that fails again.",
                    ],
                    [
                        'name' => 'Routine maintenance',
                        'description' => "Most electrical breakdowns give warning signs long before they happen. Regular maintenance catches loose connections, worn parts, overheating and overloaded circuits early, when they are cheap and easy to fix. We inspect and service the electrical systems of shops, offices, schools, clinics and factories on a schedule that suits you.\n\nAfter each visit you will know what was checked, what was fixed and what to watch. This keeps your business running and reduces the risk of unplanned power failures.",
                    ],
                    [
                        'name' => 'Rental property electrical upkeep',
                        'description' => "Landlords and property managers need electrical problems fixed quickly and safely, so tenants stay happy and the building stays protected. We handle repairs and safety checks across apartments, rental houses and commercial units, from replacing sockets and fixing tripping circuits to checking meters and common-area lighting.\n\nWe can also check each unit between tenants, so you know the wiring is safe before the next person moves in. Tell us how many units you manage and we can arrange regular visits.",
                    ],
                    [
                        'name' => 'Appliance and circuit repairs',
                        'description' => "When a cooker, water heater, fridge or other appliance stops working, the fault is sometimes in the appliance and sometimes in the wiring supplying it. We check both, so you know whether to repair the appliance or fix the circuit behind it. We repair damaged plugs, supply cables, isolators and dedicated circuits for cookers, heaters and similar equipment.\n\nIf the appliance itself needs a specialist, we will tell you honestly instead of replacing parts that will not solve the problem.",
                    ],
                ],
            ],
        ];
    }
}