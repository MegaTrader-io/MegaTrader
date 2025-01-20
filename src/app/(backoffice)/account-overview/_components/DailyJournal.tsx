import React from 'react';
import Card from "@/components/Card";
import {Table, TableBody, TableHead, TableHeader, TableRow, TableCell} from "@/components/Table";
import Image from "next/image";

function DailyJournal() {
    const journalData = [
        {
            id: 1,
            canEdit: false,
            date: "Average",
            netPnl: "-$1,610.74",
            pnlHigh: "$112.29",
            pnlLow: "-$1,612.91",
            totalContracts: 170.67,
            totalFeesComm: "$186.91",
            totalTrades: 12.67,
            avgWinningTrades: "$137.37",
            avgLosingTrades: "$209.26",
            winningTradePercentage: 29.8,
            maxConsecutiveWLTrades: "2/6",
            avgWLDuration: "00:06:09",
        },
        {
            id: 2,
            canEdit: true,
            date: "Sep 30",
            netPnl: "-$73.40",
            pnlHigh: "-$1",
            pnlLow: "-$73.40",
            totalContracts: 70,
            totalFeesComm: "$25.90",
            totalTrades: 4,
            avgWinningTrades: "$2.60",
            avgLosingTrades: "$39.90",
            winningTradePercentage: 50,
            maxConsecutiveWLTrades: "2/1",
            avgWLDuration: "00:05:05",
        },
        {
            id: 3,
            canEdit: true,
            date: "Sep 30",
            netPnl: "-$73.40",
            pnlHigh: "-$1",
            pnlLow: "-$73.40",
            totalContracts: 70,
            totalFeesComm: "$25.90",
            totalTrades: 4,
            avgWinningTrades: "$2.60",
            avgLosingTrades: "$39.90",
            winningTradePercentage: 50,
            maxConsecutiveWLTrades: "2/1",
            avgWLDuration: "00:05:05",
        },
        {
            id: 4,
            canEdit: true,
            date: "Sep 27",
            netPnl: "-$234.28",
            pnlHigh: "$6.90",
            pnlLow: "-$240.78",
            totalContracts: 158,
            totalFeesComm: "$139.28",
            totalTrades: 22,
            avgWinningTrades: "$28.07",
            avgLosingTrades: "$22.04",
            winningTradePercentage: 22.73,
            maxConsecutiveWLTrades: "2/12",
            avgWLDuration: "00:05:05",
        },
    ];

    return (
        <Card className="w-full space-y-8">
            <Table>
                <TableHead className="text-xs">
                    <TableRow className="text-white text-right align-top">
                        <TableHeader className="text-left">Daily<br/>Journal</TableHeader>
                        <TableHeader>Date</TableHeader>
                        <TableHeader>Net P&L</TableHeader>
                        <TableHeader>P&L High</TableHeader>
                        <TableHeader>P&L Low</TableHeader>
                        <TableHeader>Total<br/>Contracts</TableHeader>
                        <TableHeader>Total Fees<br/>+Comm</TableHeader>
                        <TableHeader>Total Trades</TableHeader>
                        <TableHeader>Avg. Winning<br/>Trades</TableHeader>
                        <TableHeader>Avg. Losing<br/>Trades</TableHeader>
                        <TableHeader>Winning<br/>Trade %</TableHeader>
                        <TableHeader>Max. Consec.<br/>W/L Trades</TableHeader>
                        <TableHeader>Avg. W/L<br/>Duration</TableHeader>
                    </TableRow>
                </TableHead>
                <TableBody className="text-xs">
                    {journalData.map((entry) => (
                        <TableRow key={entry.id} className="text-right text-stone-400 text-xs font-normal">
                            <TableCell className="text-left">
                                {entry.canEdit && (
                                    <button onClick={() => {
                                        console.info(entry)
                                    }}>
                                        <Image src='/assets/images/pencil.svg'
                                               alt={'pencil'}
                                               width={50}
                                               height={28}/>
                                    </button>
                                ) || '-'}
                            </TableCell>
                            <TableCell>{entry.date}</TableCell>
                            <TableCell>{entry.netPnl}</TableCell>
                            <TableCell>{entry.pnlHigh}</TableCell>
                            <TableCell>{entry.pnlLow}</TableCell>
                            <TableCell>{entry.totalContracts}</TableCell>
                            <TableCell>{entry.totalFeesComm}</TableCell>
                            <TableCell>{entry.totalTrades}</TableCell>
                            <TableCell>{entry.avgWinningTrades}</TableCell>
                            <TableCell>{entry.avgLosingTrades}</TableCell>
                            <TableCell>{entry.winningTradePercentage}%</TableCell>
                            <TableCell>{entry.maxConsecutiveWLTrades}</TableCell>
                            <TableCell>{entry.avgWLDuration}</TableCell>
                        </TableRow>
                    ))}
                </TableBody>
            </Table>
        </Card>
    );
}

export default DailyJournal;
