import {CheckIcon, XMarkIcon} from "@heroicons/react/16/solid";
import {VisitDataInterface} from "@/commons/interfaces";
import Badge from "@/components/Badge";

function ConvertedIcon({converted}: { converted: boolean }) {
    return <>
        {
            converted ? (
                <Badge size={'md'} shape={'pill'}>
                    <CheckIcon className="h-4 w-4 text-black"/>
                </Badge>
            ) : (
                <Badge variant={'error'} shape={'pill'}>
                    <XMarkIcon className="h-4 w-4 text-black"/>
                </Badge>
            )
        }

    </>

}


const URLVisitsTable = ({visits}: { visits: VisitDataInterface[] }) => {
    return (
        <div className="overflow-x-auto">
            <table className="w-full text-left border-collapse">
                <thead>
                <tr className="border-b border-gray-700 text-white">
                    <th className="p-2 text-white text-xs font-bold">URL</th>
                    <th className="p-2 text-white text-xs font-bold">Referring URL</th>
                    <th className="p-2 text-white text-xs font-bold">Converted</th>
                </tr>
                </thead>
                <tbody>
                {visits.map((visit, index) => (
                    <tr key={index} className="border-b border-gray-800">
                        <td className="py-4 text-stone-400 text-xs font-normal leading-tight">{visit.url}</td>
                        <td className="py-4 text-stone-400 text-xs font-normal leading-tight">{visit.referrer}</td>
                        <td className="py-4 flex items-center">
                            <ConvertedIcon converted={visit.converted}/>
                        </td>
                    </tr>
                ))}
                </tbody>
            </table>
        </div>
    );
};

export default URLVisitsTable;
