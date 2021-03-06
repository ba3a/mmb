package ru.mmb.terminal.model;

import java.util.ArrayList;
import java.util.Date;
import java.util.List;

import ru.mmb.terminal.model.checkpoints.CheckedState;
import ru.mmb.terminal.util.PrettyDateFormat;

public class TeamResult implements Comparable<TeamResult>
{
	private final int teamId;
	private final int userId;
	private final int deviceId;
	private final int scanPointId;
	private final String takenCheckpointNames;
	private final Date checkDateTime;
	private final Date recordDateTime;

	private Team team = null;
	private ScanPoint scanPoint = null;

	private final List<Checkpoint> takenCheckpoints = new ArrayList<Checkpoint>();

	@SuppressWarnings("unused")
	private String missedCheckpointsText;
	private String takenCheckpointsText;

	public TeamResult(int teamId, int userId, int deviceId, int scanPointId, String takenCheckpointNames, Date checkDateTime, Date recordDateTime)
	{
		this.teamId = teamId;
		this.userId = userId;
		this.deviceId = deviceId;
		this.scanPointId = scanPointId;
		this.takenCheckpointNames = takenCheckpointNames;
		this.checkDateTime = checkDateTime;
		this.recordDateTime = recordDateTime;
	}

	public Team getTeam()
	{
		return team;
	}

	public void setTeam(Team team)
	{
		this.team = team;
	}

	public int getTeamId()
	{
		return teamId;
	}

	public int getUserId()
	{
		return userId;
	}

	public int getDeviceId()
	{
		return deviceId;
	}

	public List<Checkpoint> getTakenCheckpoints()
	{
		return takenCheckpoints;
	}

	public Date getCheckDateTime()
	{
		return checkDateTime;
	}

	public Date getRecordDateTime()
	{
		return recordDateTime;
	}

	public ScanPoint getScanPoint()
	{
		return scanPoint;
	}

	public void setScanPoint(ScanPoint scanPoint)
	{
		this.scanPoint = scanPoint;
	}

	public int getScanPointId()
	{
		return scanPointId;
	}

	public LevelPoint getLevelPoint()
	{
		return scanPoint.getLevelPointByDistance(team.getDistanceId());
	}

	public void initTakenCheckpoints()
	{
		if (scanPoint == null) return;

		takenCheckpoints.clear();

		String[] pointNames = takenCheckpointNames.split(",");
		for (int i = 0; i < pointNames.length; i++)
		{
			Checkpoint checkpoint = getLevelPoint().getCheckpointByName(pointNames[i]);
			if (checkpoint == null) continue;
			takenCheckpoints.add(checkpoint);
		}

		initCheckpointsTexts();
	}

	private void initCheckpointsTexts()
	{
		LevelPoint levelPoint = getLevelPoint();
		if (levelPoint.getPointType().isFinish())
		{
			CheckedState checkedState = new CheckedState();
			checkedState.setLevelPoint(levelPoint);
			checkedState.loadTakenCheckpoints(takenCheckpoints);
			missedCheckpointsText = checkedState.getMissedCheckpointsText();
			takenCheckpointsText = checkedState.getTakenCheckpointsText();
		}
		else
		{
			missedCheckpointsText = "";
			takenCheckpointsText = "";
		}
	}

	public String getTakenCheckpointNames()
	{
		return takenCheckpointNames;
	}

	@Override
	public int compareTo(TeamResult another)
	{
		int result = recordDateTime.compareTo(another.recordDateTime);
		if (result == 0)
		{
			result = (new Integer(userId)).compareTo(new Integer(another.userId));
		}
		return result;
	}

	public String buildInfoText()
	{
		StringBuilder sb = new StringBuilder();
		sb.append(PrettyDateFormat.format(checkDateTime));
		if (getLevelPoint().getPointType().isFinish())
		    sb.append("\n").append(takenCheckpointsText);
		return sb.toString();
	}

	@Override
	public String toString()
	{
		return "TeamLevelPoint [teamId=" + teamId + ", userId=" + userId + ", deviceId=" + deviceId
		        + ", levelPointId=" + getLevelPoint().getLevelPointId() + ", takenCheckpointNames="
		        + takenCheckpointNames + ", checkDateTime=" + checkDateTime + ", recordDateTime="
		        + recordDateTime + ", takenCheckpoints=" + takenCheckpoints
		        + ", takenCheckpointsText=" + takenCheckpointsText + "]";
	}
}
